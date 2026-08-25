<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
     * Follow-up order page for existing table session
     */
    public function followupOrderPage($tableNumber)
    {
        // Get the most recent active table session for this table today
        $latestOrder = \App\Models\Order::where('table_number', $tableNumber)
                        ->where('order_type', 'dine_in')
                        ->whereDate('created_at', today())
                        ->whereNotNull('table_session_id')
                        ->whereIn('status', ['pending', 'accepted', 'preparing', 'ready'])
                        ->orderBy('created_at', 'desc')
                        ->first();

        if (!$latestOrder || !$latestOrder->table_session_id) {
            return redirect()->route('waiter.order')
                ->with('error', "No active session found for Table {$tableNumber}. Please start a new order.");
        }

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

        $isOpenDineIn = cache()->get('shop_is_open_dine_in', true);

        return view('waiter.followup-order', compact('categories', 'menuItems', 'menuItemsData', 'tableNumber', 'latestOrder', 'isOpenDineIn'));
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
}
