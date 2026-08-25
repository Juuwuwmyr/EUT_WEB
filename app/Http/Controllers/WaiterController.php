<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class WaiterController extends Controller
{
    /**
     * Waiter dashboard — shows active dine-in table orders
     */
    public function dashboard()
    {
        return view('waiter.dashboard');
    }

    /**
     * Waiter ordering page — select table from dropdown, then order for customer
     */
    public function orderPage()
    {
        $categories = \App\Models\Category::active()->orderBy('sort_order')->get();
        $menuItems  = \App\Models\MenuItem::with([
                        'category',
                        'modifierGroups' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
                        'modifierGroups.activeOptions' => fn($q) => $q->orderBy('sort_order'),
                      ])->active()->orderBy('category_id')->orderBy('sort_order')->get();

        $menuItemsData = $menuItems->map(function($item) {
            $arr = $item->toArray();
            $arr['addon_groups']    = array_values(array_filter($arr['modifier_groups'] ?? [], fn($g) => $g['type'] === 'addon'));
            $arr['modifier_groups'] = array_values(array_filter($arr['modifier_groups'] ?? [], fn($g) => $g['type'] !== 'addon'));
            return $arr;
        });

        $tables = range(1, 30);

        $isOpenDineIn = cache()->get('shop_is_open_dine_in', true);

        return view('waiter.order', compact('categories', 'menuItems', 'menuItemsData', 'tables', 'isOpenDineIn'));
    }

    /**
     * GET /waiter/orders — JSON snapshot of active dine-in orders
     */
    public function getOrders()
    {
        $orders = Order::with(['items', 'user'])
            ->where('order_type', 'dine_in')
            ->whereNotIn('status', ['cancelled'])
            ->where('is_archived', false)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'table_number' => $o->table_number,
                'status'       => $o->status,
                'status_label' => $o->status_label,
                'total'        => $o->total,
                'notes'        => $o->notes,
                'placed_at'    => $o->created_at->format('g:i A'),
                'customer'     => $o->user?->name ?? 'Guest',
                'prepared_at'  => $o->prepared_at?->format('g:i A'),
                'items'        => $o->items->map(fn($i) => [
                    'name'      => $i->item_name,
                    'qty'       => $i->quantity,
                    'subtotal'  => $i->subtotal,
                    'modifiers' => collect($i->modifiers ?? [])
                        ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
                        ->values()
                        ->toArray(),
                ])->toArray(),
            ]);

        return response()->json(['orders' => $orders]);
    }

    /**
     * POST /waiter/orders/{order}/serve — mark dine-in order as delivered (served)
     */
    public function serveOrder(Order $order)
    {
        if ($order->order_type !== 'dine_in') {
            return response()->json(['success' => false, 'message' => 'Not a dine-in order.'], 422);
        }

        if (!in_array($order->status, ['preparing', 'accepted'])) {
            return response()->json(['success' => false, 'message' => 'Order cannot be marked as served at this stage.'], 422);
        }

        $now = now();
        $order->updateQuietly([
            'status'       => 'delivered',
            'delivered_at' => $now,
            'prepared_at'  => $order->prepared_at ?? $now,
            'payment_status' => $order->payment_method === 'cash' ? 'pending' : $order->payment_status,
        ]);

        try {
            broadcast(new \App\Events\OrderStatusUpdated($order));
        } catch (\Throwable $e) {
            \Log::warning('Broadcast failed (waiter serve): ' . $e->getMessage());
        }

        \App\Models\AuditLog::record(
            action:      'order_served',
            description: auth()->user()->name . " marked order #{$order->order_number} as served.",
            model:       $order,
        );

        return response()->json(['success' => true, 'message' => "Order #{$order->order_number} marked as served."]);
    }

    /**
     * POST /waiter/orders/{order}/request-bill — flag order as bill requested
     */
    public function requestBill(Order $order)
    {
        if ($order->order_type !== 'dine_in') {
            return response()->json(['success' => false, 'message' => 'Not a dine-in order.'], 422);
        }

        $order->updateQuietly(['bill_requested' => true]);

        return response()->json(['success' => true, 'message' => "Bill requested for table {$order->table_number}."]);
    }

    /**
     * POST /waiter/place-order — place a dine-in or takeout order on behalf of a customer
     * Bypasses customer-facing location checks and auth requirements.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required',
            'items.*.qty'    => 'required|integer|min:1|max:99',
            'items.*.modifiers' => 'nullable|array',
            'order_type'     => 'required|in:dine_in,pickup',
            'table_number'   => 'required_if:order_type,dine_in|nullable|string|max:20',
            'notes'          => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:cash,gcash,card',
        ]);

        DB::beginTransaction();
        try {
            $subtotal  = 0;
            $lineItems = [];

            foreach ($request->items as $line) {
                $menuItemId = (int) explode('_', $line['id'])[0];
                $menuItem   = MenuItem::find($menuItemId);

                if (!$menuItem) {
                    DB::rollBack();
                    return response()->json([
                        'success'       => false,
                        'message'       => 'One or more items are no longer available.',
                        'stale_item_id' => $menuItemId,
                        'clear_cart'    => true,
                    ], 422);
                }

                $qty   = (int) $line['qty'];
                $price = (float) $menuItem->price;

                $modifierSummary = [];
                if (!empty($line['modifiers']) && is_array($line['modifiers'])) {
                    foreach ($line['modifiers'] as $mod) {
                        $adj  = (float) ($mod['price_adjustment'] ?? 0);
                        $type = $mod['price_type'] ?? 'none';
                        if ($type === 'add')                   { $price += $adj; }
                        elseif ($type === 'replace' && $adj > 0) { $price = $adj; }
                        $modifierSummary[] = [
                            'type'             => $mod['type']  ?? 'modifier',
                            'name'             => $mod['name']  ?? '',
                            'price_type'       => $type,
                            'price_adjustment' => $adj,
                        ];
                    }
                }

                $price    = round($price, 2);
                $lineSub  = round($price * $qty, 2);
                $subtotal = round($subtotal + $lineSub, 2);

                $lineItems[] = [
                    'menu_item_id' => $menuItemId,
                    'item_name'    => $menuItem->name,
                    'image'        => $menuItem->image,
                    'unit_price'   => $price,
                    'quantity'     => $qty,
                    'subtotal'     => $lineSub,
                    'modifiers'    => !empty($modifierSummary) ? $modifierSummary : null,
                ];
            }

            $total = $subtotal; // no delivery fee for waiter-placed orders

            // Dine-in: try to merge into existing pending order at same table
            if ($request->order_type === 'dine_in' && $request->table_number) {
                $existingOrder = Order::where('order_type', 'dine_in')
                    ->where('table_number', $request->table_number)
                    ->where('status', 'pending')
                    ->when(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'ordering_locked'),
                        fn($q) => $q->where('ordering_locked', false)
                    )
                    ->whereDate('created_at', today())
                    ->latest()
                    ->first();

                if ($existingOrder) {
                    foreach ($lineItems as $item) {
                        $existingOrder->items()->create($item);
                    }
                    $newSubtotal = $existingOrder->items()->sum('subtotal');
                    $existingOrder->update([
                        'subtotal' => round($newSubtotal, 2),
                        'total'    => round($newSubtotal, 2),
                        'notes'    => $request->notes
                            ? ($existingOrder->notes ? $existingOrder->notes . ' | ' . $request->notes : $request->notes)
                            : $existingOrder->notes,
                    ]);
                    $existingOrder->refresh();
                    DB::commit();
                    try { broadcast(new OrderStatusUpdated($existingOrder))->toOthers(); } catch (\Throwable $e) {}
                    return response()->json(['success' => true, 'order_id' => $existingOrder->id, 'order_number' => $existingOrder->order_number, 'total' => round($newSubtotal, 2), 'merged' => true]);
                }
            }

            // Inherit table session for dine_in
            $tableSessionId = null;
            if ($request->order_type === 'dine_in' && $request->table_number) {
                $activeSession = Order::where('order_type', 'dine_in')
                    ->where('table_number', $request->table_number)
                    ->when(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'ordering_locked'), fn($q) => $q->where('ordering_locked', false))
                    ->when(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'payment_status'), fn($q) => $q->where('payment_status', '!=', 'paid'))
                    ->whereDate('created_at', today())
                    ->whereNotNull('table_session_id')
                    ->latest()
                    ->value('table_session_id');
                $tableSessionId = $activeSession ?? \Illuminate\Support\Str::uuid();
            }

            $order = Order::create([
                'user_id'          => null, // staff placing on behalf of customer
                'status'           => 'pending',
                'order_type'       => $request->order_type,
                'subtotal'         => $subtotal,
                'delivery_fee'     => 0,
                'total'            => $total,
                'payment_method'   => $request->payment_method ?? 'cash',
                'payment_status'   => 'pending',
                'delivery_address' => $request->order_type === 'dine_in'
                    ? 'Dine-in · Table ' . $request->table_number
                    : 'Counter Pickup',
                'table_number'     => $request->order_type === 'dine_in' ? $request->table_number : null,
                'notes'            => $request->notes,
                'table_session_id' => $tableSessionId,
            ]);

            foreach ($lineItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();
            try { broadcast(new OrderStatusUpdated($order))->toOthers(); } catch (\Throwable $e) {}

            return response()->json([
                'success'      => true,
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'total'        => $total,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Waiter placeOrder failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Order failed. Please try again.'], 500);
        }
    }
}
