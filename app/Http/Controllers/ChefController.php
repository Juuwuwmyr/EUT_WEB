<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Rider;
use Illuminate\Http\Request;

class ChefController extends Controller
{
    /**
     * Accept is owned by the Admin — admin accepts and auto-starts cooking.
     */
    public function acceptOrder(Order $order)
    {
        return $this->kitchenActionResponse(false, 'Orders are accepted by the Admin. They automatically start cooking.');
    }

    /**
     * Display the chef's kitchen board.
     */
    public function dashboard()
    {
        $orders = $this->getKitchenOrders();

        $availableRiders = Rider::with('user')
            ->where('is_available', true)
            ->withCount(['orders as active_orders' => function ($q) {
                $q->whereIn('status', ['rider_assigned', 'out_for_delivery']);
            }])
            ->orderBy('active_orders') // free riders first, busy riders second
            ->get()
            ->map(fn($r) => [
                'id'    => $r->id,
                'name'  => $r->user->name . ($r->active_orders > 0 ? ' 🏍️ (' . $r->active_orders . ' order' . ($r->active_orders > 1 ? 's' : '') . ')' : ''),
                'phone' => $r->phone,
                'busy'  => $r->active_orders > 0,
            ]);

        $todayOrders    = \App\Models\Order::whereDate('created_at', today())->count();
        $deliveredToday = \App\Models\Order::where('status', 'delivered')->whereDate('delivered_at', today())->count();
        $revenueToday   = \App\Models\Order::where('status', 'delivered')->whereDate('delivered_at', today())->sum('total');
        $pendingCount   = \App\Models\Order::where('status', 'pending')->count();
        $todayDelivery  = \App\Models\Order::whereDate('created_at', today())->where('order_type', 'delivery')->count();
        $todayDineIn    = \App\Models\Order::whereDate('created_at', today())->where('order_type', 'dine_in')->count();
        $todayPickup    = \App\Models\Order::whereDate('created_at', today())->where('order_type', 'pickup')->count();

        return view('chef.dashboard', [
            'newOrders'          => $orders['new'],
            'queuedOrders'       => $orders['queued'],
            'cookingOrders'      => $orders['cooking'],
            'readyOrders'        => $orders['ready'],
            'availableRiders'    => $availableRiders,
            'todayOrders'        => $todayOrders,
            'deliveredToday'     => $deliveredToday,
            'revenueToday'       => $revenueToday,
            'pendingCount'       => $pendingCount,
            'availableRiderCount'=> $availableRiders->count(),
            'todayDelivery'      => $todayDelivery,
            'todayDineIn'        => $todayDineIn,
            'todayPickup'        => $todayPickup,
        ]);
    }

    /**
     * Get orders for AJAX refresh.
     */
    public function getOrders()
    {
        $orders = $this->getKitchenOrders();

        return response()->json([
            'new'     => $this->formatKitchenOrders($orders['new']),
            'queued'  => $this->formatKitchenOrders($orders['queued']),
            'cooking' => $this->formatKitchenOrders($orders['cooking']),
            'ready'   => $this->formatKitchenOrders($orders['ready']),
        ]);
    }

    /**
     * Remove a single item from a dine-in order in the kitchen queue.
     * Recalculates order totals. If this was the last item, cancels the whole order.
     */
    public function cancelItem(Request $request, Order $order, \App\Models\OrderItem $item)
    {
        // Only dine-in orders support per-item removal from the kitchen
        if ($order->order_type !== 'dine_in') {
            return $this->kitchenActionResponse(false, 'Per-item removal is only available for dine-in orders.');
        }

        // Order must still be in a cancellable state
        if (!$order->isCancellable()) {
            return $this->kitchenActionResponse(false, 'This order can no longer be modified.');
        }

        // Make sure the item actually belongs to this order
        if ($item->order_id !== $order->id) {
            return $this->kitchenActionResponse(false, 'Item does not belong to this order.');
        }

        // How many to cancel — defaults to the full quantity (original behaviour)
        $cancelQty = (int) $request->input('qty', $item->quantity);
        $cancelQty = max(1, min($cancelQty, $item->quantity)); // clamp to [1, item qty]

        if ($cancelQty < $item->quantity) {
            // ── Partial removal: reduce quantity and recalculate item subtotal ──
            $newQty      = $item->quantity - $cancelQty;
            $newSubtotal = round($item->unit_price * $newQty, 2);

            $item->updateQuietly([
                'quantity' => $newQty,
                'subtotal' => $newSubtotal,
            ]);

            $order->load('items');
            $orderSubtotal = round($order->items->sum('subtotal'), 2);

            $order->updateQuietly([
                'subtotal' => $orderSubtotal,
                'total'    => $orderSubtotal,
            ]);
            $order->refresh();

            broadcast(new OrderStatusUpdated($order));

            AuditLog::record(
                action:      'order_item_qty_reduced',
                description: "Kitchen reduced {$item->item_name} qty by {$cancelQty} on {$order->order_number} (now {$newQty}×).",
                model:       $order,
                newValues:   ['item_id' => $item->id, 'cancelled_qty' => $cancelQty, 'remaining_qty' => $newQty, 'new_subtotal' => $orderSubtotal],
            );

            return response()->json([
                'success'      => true,
                'message'      => "{$cancelQty}× {$item->item_name} removed. {$newQty}× still on the order.",
                'new_subtotal' => $orderSubtotal,
                'items_left'   => $order->items->count(),
            ]);
        }

        // ── Full removal: delete the item row ──
        $item->delete();

        // Reload remaining items
        $order->load('items');
        $remaining = $order->items;

        if ($remaining->isEmpty()) {
            // No items left — cancel the whole order
            $order->updateQuietly([
                'status'        => 'cancelled',
                'cancel_reason' => 'All items removed by kitchen staff',
                'cancelled_at'  => now(),
                'subtotal'      => 0,
                'total'         => 0,
            ]);
            $order->refresh();

            broadcast(new OrderStatusUpdated($order));

            AuditLog::record(
                action:      'order_cancelled',
                description: "Kitchen removed last item — {$order->order_number} cancelled.",
                model:       $order,
            );

            return $this->kitchenActionResponse(true, "All items removed — order #{$order->order_number} has been cancelled.");
        }

        // Recalculate totals from remaining items
        $newSubtotal = round($remaining->sum('subtotal'), 2);

        $order->updateQuietly([
            'subtotal' => $newSubtotal,
            'total'    => $newSubtotal, // dine-in has no delivery fee
        ]);
        $order->refresh();

        broadcast(new OrderStatusUpdated($order));

        AuditLog::record(
            action:      'order_item_removed',
            description: "Kitchen removed item from {$order->order_number}.",
            model:       $order,
            newValues:   ['items_remaining' => $remaining->count(), 'new_subtotal' => $newSubtotal],
        );

        return response()->json([
            'success'      => true,
            'message'      => "Item removed from order #{$order->order_number}.",
            'new_subtotal' => $newSubtotal,
            'items_left'   => $remaining->count(),
        ]);
    }

    /**
     * Start cooking an order.
     */
    public function startCooking(Order $order)
    {
        if ($order->status !== 'accepted') {
            return $this->kitchenActionResponse(false, 'Only accepted orders can start cooking.');
        }

        $order->updateQuietly([
            'status'      => 'preparing',
            'prepared_at' => null,
        ]);
        $order->refresh();

        broadcast(new OrderStatusUpdated($order));

        AuditLog::record(
            action:      'order_cooking_started',
            description: "Chef started cooking {$order->order_number}.",
            model:       $order,
        );

        return $this->kitchenActionResponse(true, "Order #{$order->order_number} is now cooking.");
    }

    /**
     * Mark an order as ready for pickup.
     * Accepts both 'accepted' and 'preparing' statuses — if the order is still
     * 'accepted' (i.e. chef never pressed Start Cooking, which is now removed
     * from the UI), we auto-transition it to 'preparing' and mark ready in
     * one step.
     */
    public function markReady(Order $order)
    {
        if (!in_array($order->status, ['accepted', 'preparing'])) {
            return $this->kitchenActionResponse(false, 'Order cannot be marked ready at this stage.');
        }

        if ($order->prepared_at) {
            return $this->kitchenActionResponse(false, 'Order is already marked ready.');
        }

        $update = ['prepared_at' => now()];
        if ($order->status === 'accepted') {
            $update['status'] = 'preparing';
        }

        $order->updateQuietly($update);
        $order->refresh();

        broadcast(new OrderStatusUpdated($order));

        AuditLog::record(
            action:      'order_marked_ready',
            description: "Chef marked {$order->order_number} as ready.",
            model:       $order,
        );

        return $this->kitchenActionResponse(true, "Order #{$order->order_number} is ready for pickup.");
    }

    /**
     * Assign a rider to a cooking/ready order from the kitchen board.
     */
    public function assignRider(Request $request, Order $order)
    {
        $request->validate(['rider_id' => 'required|exists:riders,id']);

        if (!$order->isAssignable()) {
            return $this->kitchenActionResponse(false, 'Order cannot be assigned at this stage.');
        }

        $order->updateQuietly([
            'rider_id'    => $request->rider_id,
            'status'      => $order->status === 'out_for_delivery' ? 'out_for_delivery' : 'rider_assigned',
            'assigned_at' => now(),
            'prepared_at' => $order->prepared_at ?? now(),
        ]);
        $order->refresh();

        broadcast(new OrderStatusUpdated($order));

        AuditLog::record(
            action:      'rider_assigned',
            description: "Chef assigned rider to {$order->order_number}.",
            model:       $order,
            newValues:   ['rider_id' => $request->rider_id],
        );

        return $this->kitchenActionResponse(true, "Rider assigned to order #{$order->order_number}.");
    }

    /**
     * Get the kitchen receipt for a single order.
     */
    public function receipt(Order $order)
    {
        $order->load(['items', 'user']);
        return view('admin.partials.kitchen-receipt', compact('order'));
    }

    /**
     * Receipt for a completed dine-in order.
     * Loads ALL orders from the same table session (same table_session_id or
     * same table + same day) so the receipt shows the full combined bill.
     */
    public function tableReceipt(Order $order)
    {
        $order->load(['items', 'user']);
        $tableNumber = $order->table_number;

        if ($tableNumber) {
            // Fetch all orders for the same table today (excluding cancelled)
            // Group by table_session_id if available, otherwise same table + same day
            $query = Order::with('items')
                ->where('table_number', $tableNumber)
                ->where('order_type', 'dine_in')
                ->whereNotIn('status', ['cancelled'])
                ->whereDate('created_at', $order->created_at->toDateString());

            // Prefer grouping by session ID so multi-session days work correctly
            if ($order->table_session_id) {
                $query->where('table_session_id', $order->table_session_id);
            }

            $orders = $query->oldest()->get();

            // Ensure the triggered order is always included even if query missed it
            if ($orders->where('id', $order->id)->isEmpty()) {
                $orders = $orders->push($order)->sortBy('id')->values();
            }

            return view('admin.partials.table-receipt', compact('orders', 'tableNumber'));
        }

        return view('admin.partials.kitchen-receipt', compact('order'));
    }

    /**
     * Combined bill for all of today's dine-in orders at a given table number.
     *
     * Unlike tableReceipt() which filters by table_session_id, this method
     * gathers EVERY non-cancelled order for the table today — across ALL sessions.
     * Use this when a customer placed multiple pahabol orders that ended up in
     * different sessions (because earlier batches were already cooking/served).
     *
     * Route: GET /chef/orders/table-bill/{table}
     */
    public function tableReceiptByNumber(string $tableNumber)
    {
        if (!preg_match('/^\d{1,3}$/', $tableNumber)) {
            abort(404);
        }

        $orders = Order::with('items')
            ->where('table_number', $tableNumber)
            ->where('order_type', 'dine_in')
            ->whereNotIn('status', ['cancelled'])
            ->whereDate('created_at', today())
            ->oldest()
            ->get();

        if ($orders->isEmpty()) {
            abort(404, 'No orders found for Table ' . $tableNumber . ' today.');
        }

        return view('admin.partials.table-receipt', compact('orders', 'tableNumber'));
    }

    /**
     * Get the kitchen ticket (no prices) for an order.
     */
    public function kitchenTicket(Order $order, Request $request)
    {
        $order->load(['items', 'user']);

        // If addon_ids is passed, only show those specific item IDs (pahabol print)
        $addonIds = null;
        if ($request->filled('addon_ids')) {
            $addonIds = array_map('intval', explode(',', $request->query('addon_ids')));
            $order->setRelation('items', $order->items->whereIn('id', $addonIds)->values());
        }

        return view('admin.partials.kitchen-ticket', compact('order', 'addonIds'));
    }

    /**
     * Takeout slip — printed when rider confirms pickup (out_for_delivery).
     * Shows full receipt with rider name, items + prices, and total.
     */
    public function takeoutSlip(Order $order)
    {
        $order->load(['items', 'user', 'rider.user']);
        return view('admin.partials.takeout-slip', compact('order'));
    }


    /**
     * Mark all active kitchen orders for a table session as ready in one shot.
     * Accepts either a table_session_id (UUID) or a table_number (dine-in fallback).
     *
     * Route: POST /chef/orders/table-session/{key}/ready
     */
    public function markTableReady(string $key)
    {
        // Try by table_session_id first, then by table_number (today's dine-in)
        $orders = Order::where('table_session_id', $key)
            ->where('status', 'preparing')
            ->whereNull('prepared_at')
            ->get();

        if ($orders->isEmpty()) {
            // Fallback: treat key as table number
            $orders = Order::where('table_number', $key)
                ->where('order_type', 'dine_in')
                ->whereIn('status', ['accepted', 'preparing'])
                ->whereNull('prepared_at')
                ->whereDate('created_at', today())
                ->get();
        }

        if ($orders->isEmpty()) {
            return $this->kitchenActionResponse(false, 'No active cooking orders found for this table.');
        }

        $now = now();
        foreach ($orders as $order) {
            // Accepted orders: transition to preparing + mark ready in one step
            if ($order->status === 'accepted') {
                $order->updateQuietly(['status' => 'preparing', 'prepared_at' => $now]);
            } else {
                $order->updateQuietly(['prepared_at' => $now]);
            }
            $order->refresh();
            broadcast(new OrderStatusUpdated($order));
        }

        $tableLabel = $orders->first()->table_number
            ? 'Table ' . $orders->first()->table_number
            : 'this table';

        AuditLog::record(
            action:      'table_marked_ready',
            description: "Kitchen marked {$tableLabel} as ready ({$orders->count()} order(s)).",
        );

        return $this->kitchenActionResponse(true, "{$tableLabel} — all orders marked ready.");
    }

    /**
     * Internal helper to fetch categorized kitchen orders.
     */
    private function getKitchenOrders(): array
    {
        // New = pending, waiting for admin to accept
        $newOrders = Order::with(['user', 'items'])
            ->where('status', 'pending')
            ->oldest()
            ->get();

        // Queue = accepted by admin — these go straight into "cooking" on the kitchen display
        // (no "Start Cooking" step needed; acceptance = start of cooking)
        $queuedOrders = Order::with(['user', 'items'])
            ->where('status', 'accepted')
            ->oldest()
            ->get();

        // Cooking = preparing but not yet marked ready (prepared_at is null)
        $cookingOrders = Order::with(['user', 'items'])
            ->where('status', 'preparing')
            ->whereNull('prepared_at')
            ->oldest()
            ->get();

        $readyForDelivery = Order::with(['user', 'items', 'rider.user'])
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('status', 'preparing')->whereNotNull('prepared_at');
                })->orWhereIn('status', ['rider_assigned', 'out_for_delivery']);
            })
            ->orderByRaw("CASE
                WHEN status = 'preparing' THEN 1
                WHEN status = 'rider_assigned' THEN 2
                WHEN status = 'out_for_delivery' THEN 3
                ELSE 4 END")
            ->oldest('prepared_at')
            ->get();

        return [
            'new'     => $newOrders,      // Pending — admin needs to accept
            'queued'  => $queuedOrders,   // Accepted — chef clicks Start Cooking
            'cooking' => $cookingOrders,  // Actively being prepared
            'ready'   => $readyForDelivery->values(),
        ];
    }

    /**
     * Format a collection of kitchen orders.
     */
    private function formatKitchenOrders($orders): array
    {
        return collect($orders)->map(fn ($o) => $this->formatKitchenOrder($o))->values()->all();
    }

    /**
     * Format a single order for the kitchen dashboard.
     */
    private function formatKitchenOrder(Order $order): array
    {
        $delivery = $this->kitchenDeliveryMeta($order);

        return [
            'id'              => $order->id,
            'order_number'    => $order->order_number,
            'status'          => $order->status,
            'updated_at'      => $order->updated_at?->toISOString(),
            'order_type'      => $order->order_type,
            'order_type_label'=> $order->order_type_label,
            'order_type_icon' => $order->order_type_icon,
            'customer'        => $order->user?->name ?? 'Guest',
            'notes'           => $order->notes,
            'table_number'    => $order->table_number,
            'placed_at'       => $order->created_at->format('g:i A'),
            'elapsed_mins'    => (int) $order->created_at->diffInMinutes(now()),
            'accepted_at'     => $order->accepted_at?->format('g:i A'),
            'prepared_at'     => $order->prepared_at?->format('g:i A'),
            'assigned_at'     => $order->assigned_at?->format('g:i A'),
            'picked_up_at'    => $order->picked_up_at?->format('g:i A'),
            'rider_name'      => $order->rider?->user?->name,
            'rider_id'        => $order->rider_id,
            'table_session_id'=> $order->table_session_id,
            'subtotal'        => (float) $order->subtotal,
            'delivery_fee'    => (float) $order->delivery_fee,
            'total'           => (float) $order->total,
            'payment_method'  => $order->payment_method,
            'delivery_status' => $delivery['status'],
            'delivery_label'  => $delivery['label'],
            'delivery_detail' => $delivery['detail'],
            'delivery_color'  => $delivery['color'],
            'delivery_bg'     => $delivery['bg'],
            'items'           => $order->items->map(fn ($i) => [
                'id'        => $i->id,
                'name'      => $i->item_name,
                'qty'       => $i->quantity,
                'price'     => (float) $i->unit_price,
                'subtotal'  => (float) $i->subtotal,
                'image'     => $i->image ? asset($i->image) : asset('images/hero-burger.webp'),
                'modifiers' => collect($i->modifiers ?? [])
                    ->filter(fn ($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
                    ->values()
                    ->all(),
            ])->all(),
        ];
    }

    /**
     * Get delivery metadata for an order in the kitchen.
     */
    private function kitchenDeliveryMeta(Order $order): array
    {
        if ($order->order_type !== 'delivery') {
            return [
                'status' => 'ready_pickup',
                'label'  => $order->order_type === 'pickup' ? 'Ready for Pickup' : 'Ready for Dine-in',
                'detail' => $order->prepared_at
                    ? 'Marked ready at ' . $order->prepared_at->format('g:i A')
                    : 'Food is ready',
                'color'  => '#10b981',
                'bg'     => 'rgba(16,185,129,.14)',
            ];
        }

        if ($order->status === 'out_for_delivery') {
            return [
                'status' => 'picked_up',
                'label'  => 'Picked Up — On the Way',
                'detail' => $order->picked_up_at
                    ? 'Left kitchen at ' . $order->picked_up_at->format('g:i A')
                    : 'Out for delivery',
                'color'  => '#8b5cf6',
                'bg'     => 'rgba(139,92,246,.14)',
            ];
        }

        if ($order->status === 'rider_assigned') {
            $rider = $order->rider?->user?->name ?? 'Rider';

            return [
                'status' => 'rider_assigned',
                'label'  => "Hand to Rider: {$rider}",
                'detail' => $order->assigned_at
                    ? 'Rider assigned at ' . $order->assigned_at->format('g:i A')
                    : 'Waiting for rider pickup',
                'color'  => '#2563eb',
                'bg'     => 'rgba(37,99,235,.14)',
            ];
        }

        return [
            'status' => 'waiting_rider',
            'label'  => 'Ready — Waiting for Rider',
            'detail' => $order->prepared_at
                ? 'Food ready at ' . $order->prepared_at->format('g:i A')
                : 'Ready for delivery',
            'color'  => '#10b981',
            'bg'     => 'rgba(16,185,129,.14)',
        ];
    }

    /**
     * Helper for standard kitchen action responses.
     */
    private function kitchenActionResponse(bool $success, string $message)
    {
        if (request()->expectsJson()) {
            return response()->json(['success' => $success, 'message' => $message]);
        }

        return $success
            ? back()->with('success', $message)
            : back()->with('error', $message);
    }
}
