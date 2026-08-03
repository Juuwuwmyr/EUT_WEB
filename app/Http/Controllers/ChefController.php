<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
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
     * Start cooking an order.
     */
    public function startCooking(Order $order)
    {
        if ($order->status !== 'accepted') {
            return $this->kitchenActionResponse(false, 'Only accepted orders can start cooking.');
        }

        $order->update([
            'status'      => 'preparing',
            'prepared_at' => null,
        ]);

        broadcast(new OrderStatusUpdated($order));

        return $this->kitchenActionResponse(true, "Order #{$order->order_number} is now cooking.");
    }

    /**
     * Mark an order as ready for pickup.
     */
    public function markReady(Order $order)
    {
        if ($order->status !== 'preparing') {
            return $this->kitchenActionResponse(false, 'Only orders being cooked can be marked ready.');
        }

        if ($order->prepared_at) {
            return $this->kitchenActionResponse(false, 'Order is already marked ready.');
        }

        $order->update(['prepared_at' => now()]);

        broadcast(new OrderStatusUpdated($order));

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

        $order->update([
            'rider_id'    => $request->rider_id,
            'status'      => 'rider_assigned',
            'assigned_at' => now(),
            'prepared_at' => $order->prepared_at ?? now(),
        ]);

        broadcast(new OrderStatusUpdated($order));

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
     * Get a combined receipt for all delivered dine-in orders at the same table.
     * Falls back to a single-order receipt if the order has no table number.
     */
    public function tableReceipt(Order $order)
    {
        $order->load(['items', 'user']);

        if (!$order->table_number) {
            return view('admin.partials.kitchen-receipt', compact('order'));
        }

        // Gather all delivered orders for this table from today (same table session)
        $orders = Order::with(['items'])
            ->where('order_type', 'dine_in')
            ->where('table_number', $order->table_number)
            ->where('status', 'delivered')
            ->whereDate('created_at', $order->created_at->toDateString())
            ->oldest()
            ->get();

        // Safety: if somehow empty, fall back to the single order
        if ($orders->isEmpty()) {
            $orders = collect([$order]);
        }

        $tableNumber = $order->table_number;

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
     * Internal helper to fetch categorized kitchen orders.
     */
    private function getKitchenOrders(): array
    {
        // New = pending, waiting for admin to accept
        $newOrders = Order::with(['user', 'items'])
            ->where('status', 'pending')
            ->oldest()
            ->get();

        // Queue = accepted by admin, waiting for chef to start cooking
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
