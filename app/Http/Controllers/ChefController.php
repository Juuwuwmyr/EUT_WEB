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
     * Start cooking — no longer needed as accept auto-sets preparing.
     * Kept for backward compat but proxies to markReady intent.
     */
    public function startCooking(Order $order)
    {
        // Admin accept already sets status=preparing, so this is a no-op safety valve
        if (!in_array($order->status, ['accepted', 'preparing'])) {
            return $this->kitchenActionResponse(false, 'Order is not in a cookable state.');
        }

        if ($order->status === 'accepted') {
            // Edge case: order was accepted without auto-preparing — push it forward
            $order->update(['status' => 'preparing']);
            broadcast(new OrderStatusUpdated($order));
        }

        return $this->kitchenActionResponse(true, "Order #{$order->order_number} is now cooking.");
    }

    /**
     * Display the chef's kitchen board.
     */
    public function dashboard()
    {
        $orders = $this->getKitchenOrders();

        $availableRiders = Rider::with('user')
            ->where('is_available', true)
            ->get()
            ->map(fn($r) => ['id' => $r->id, 'name' => $r->user->name, 'phone' => $r->phone]);

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
     * Get the kitchen receipt for an order.
     */
    public function receipt(Order $order)
    {
        $order->load(['items', 'user']);
        return view('admin.partials.kitchen-receipt', compact('order'));
    }

    /**
     * Internal helper to fetch categorized kitchen orders.
     */
    private function getKitchenOrders(): array
    {
        // Admin accept sets status=preparing directly, so 'accepted' is a transient state.
        // Show any 'accepted' orders in cooking column as a safety net.
        $active = Order::with(['user', 'items'])
            ->whereIn('status', ['preparing', 'accepted'])
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

        // Cooking = preparing but not yet marked ready (prepared_at is null)
        $cooking = $active->where('status', 'preparing')->whereNull('prepared_at')->values();
        // Also catch any order still in 'accepted' (edge case)
        $cooking = $cooking->merge($active->where('status', 'accepted')->values())->values();

        return [
            'new'     => collect(), // Admin accepts — chef never sees 'pending'
            'queued'  => collect(), // No queued step; admin accept → straight to cooking
            'cooking' => $cooking,
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
            'order_type'      => $order->order_type,
            'order_type_label'=> $order->order_type_label,
            'order_type_icon' => $order->order_type_icon,
            'customer'        => $order->user?->name ?? 'Guest',
            'notes'           => $order->notes,
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
