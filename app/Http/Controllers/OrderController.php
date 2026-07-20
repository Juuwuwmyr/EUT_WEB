<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ── POST /orders — place an order ──────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required',
            'items.*.qty'      => 'required|integer|min:1|max:99',
            'items.*.modifiers'=> 'nullable|array',
            'delivery_address' => 'required|string|max:255',
            'delivery_barangay'=> 'nullable|string|max:100',
            'delivery_lat'     => 'nullable|numeric|between:-90,90',
            'delivery_lng'     => 'nullable|numeric|between:-180,180',
            'payment_method'   => 'required|in:cash,gcash,card',
            'notes'            => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $subtotal  = 0;
            $lineItems = [];

            foreach ($request->items as $line) {
                // Cart IDs may be composite like "5_12-14" — extract the base menu item ID
                $menuItemId = (int) explode('_', $line['id'])[0];
                $menuItem   = MenuItem::findOrFail($menuItemId);
                $qty        = (int) $line['qty'];
                $price      = (float) $menuItem->price;

                // Build a clean, normalised modifiers snapshot
                $modifierSummary = [];
                if (!empty($line['modifiers']) && is_array($line['modifiers'])) {
                    foreach ($line['modifiers'] as $mod) {
                        $adj  = (float) ($mod['price_adjustment'] ?? 0);
                        $type = $mod['price_type'] ?? 'none';

                        // Only 'add' type adjusts the running price
                        if ($type === 'add') {
                            $price += $adj;
                        } elseif ($type === 'replace' && $adj > 0) {
                            $price = $adj;
                        }

                        $modifierSummary[] = [
                            'type'             => $mod['type']             ?? 'modifier',
                            'name'             => $mod['name']             ?? '',
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
                    'image'        => $menuItem->image,   // snapshot the image path
                    'unit_price'   => $price,
                    'quantity'     => $qty,
                    'subtotal'     => $lineSub,
                    'modifiers'    => !empty($modifierSummary) ? $modifierSummary : null,
                ];
            }

            // Delivery fee: free if subtotal ≥ ₱500, otherwise ₱50
            $deliveryFee = $subtotal >= 500 ? 0 : 50;
            $total       = round($subtotal + $deliveryFee, 2);

            $order = Order::create([
                'user_id'          => auth()->id(),
                'status'           => 'pending',
                'subtotal'         => $subtotal,
                'delivery_fee'     => $deliveryFee,
                'total'            => $total,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'pending',
                'delivery_address' => $request->delivery_address,
                'delivery_barangay'=> $request->delivery_barangay,
                'delivery_lat'     => $request->delivery_lat,
                'delivery_lng'     => $request->delivery_lng,
                'notes'            => $request->notes,
            ]);

            foreach ($lineItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'total'        => $total,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Order store failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Order failed. Please try again.'], 500);
        }
    }

    // ── GET /orders/{order} — order status for tracking ───
    public function show(Order $order)
    {
        // Only the owner can view their order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items', 'rider.user']);

        return response()->json([
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'status'           => $order->status,
            'status_label'     => $order->status_label,
            'total'            => $order->total,
            'delivery_address' => $order->delivery_address,
            'delivery_lat'     => $order->delivery_lat,
            'delivery_lng'     => $order->delivery_lng,
            'payment_method'   => $order->payment_method,
            'placed_at'        => $order->created_at->format('g:i A'),
            'accepted_at'      => $order->accepted_at?->format('g:i A'),
            'assigned_at'      => $order->assigned_at?->format('g:i A'),
            'picked_up_at'     => $order->picked_up_at?->format('g:i A'),
            'delivered_at'     => $order->delivered_at?->format('g:i A'),
            'rider'            => ($order->rider && $order->rider->user) ? [
                'name'    => $order->rider->user->name,
                'phone'   => $order->rider->phone,
                'rating'  => $order->rider->rating,
                'lat'     => $order->rider->current_lat,
                'lng'     => $order->rider->current_lng,
            ] : null,
            'items' => $order->items->map(fn($i) => [
                'name'      => $i->item_name,
                'qty'       => $i->quantity,
                'price'     => $i->unit_price,
                'subtotal'  => $i->subtotal,
                'image'     => $i->image ? asset($i->image) : asset('images/hero-burger.jpg'),
                'modifiers' => $i->modifiers ?? [],
            ]),
        ]);
    }

    // ── PATCH /orders/{order}/cancel ────────────────────────
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        if (!$order->isCancellable()) {
            return response()->json(['success' => false, 'message' => 'Order cannot be cancelled at this stage.'], 422);
        }

        $order->update([
            'status'       => 'cancelled',
            'cancel_reason'=> $request->input('reason', 'Cancelled by customer'),
            'cancelled_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    // ── PATCH /orders/{order}/set-coords — save geocoded delivery coords ──
    public function setCoords(Request $request, Order $order)
    {
        // Allow order owner OR the assigned rider to update coords
        $user = auth()->user();
        $isOwner = $order->user_id === $user->id;
        $isRider = $user->isRider() && $order->rider_id === $user->rider?->id;

        if (!$isOwner && !$isRider) abort(403);

        // Only update if coords are still null (don't overwrite real GPS data)
        if ($order->delivery_lat && $order->delivery_lng) {
            return response()->json(['success' => true, 'skipped' => true]);
        }

        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $order->update([
            'delivery_lat' => $request->lat,
            'delivery_lng' => $request->lng,
        ]);

        return response()->json(['success' => true]);
    }

    // ── GET /orders — customer order history (grouped by status) ───
    public function index()
    {
        $orders = Order::with(['items', 'rider.user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $active    = [];
        $past      = [];
        $cancelled = [];

        foreach ($orders as $order) {
            $data = [
                'id'               => $order->id,
                'order_number'     => $order->order_number,
                'status'           => $order->status,
                'status_label'     => $order->status_label,
                'subtotal'         => $order->subtotal,
                'delivery_fee'     => $order->delivery_fee,
                'total'            => $order->total,
                'delivery_address' => $order->delivery_address,
                'delivery_lat'     => $order->delivery_lat,
                'delivery_lng'     => $order->delivery_lng,
                'payment_method'   => $order->payment_method,
                'notes'            => $order->notes,
                'cancel_reason'    => $order->cancel_reason,
                'placed_at'        => $order->created_at->format('M d, Y g:i A'),
                'accepted_at'      => $order->accepted_at?->format('g:i A'),
                'assigned_at'      => $order->assigned_at?->format('g:i A'),
                'picked_up_at'     => $order->picked_up_at?->format('g:i A'),
                'delivered_at'     => $order->delivered_at?->format('g:i A'),
                'cancelled_at'     => $order->cancelled_at?->format('g:i A'),
                'rider'            => ($order->rider && $order->rider->user) ? [
                    'name'    => $order->rider->user->name,
                    'phone'   => $order->rider->phone,
                    'rating'  => $order->rider->rating,
                    'lat'     => $order->rider->current_lat,
                    'lng'     => $order->rider->current_lng,
                ] : null,
                'items' => $order->items->map(function($i) {
                    return [
                        'name'      => $i->item_name,
                        'qty'       => $i->quantity,
                        'price'     => $i->unit_price,
                        'subtotal'  => $i->subtotal,
                        'image'     => $i->image ? asset($i->image) : asset('images/hero-burger.jpg'),
                        'modifiers' => $i->modifiers ?? [],
                    ];
                }),
            ];

            if ($order->status === 'cancelled') {
                $cancelled[] = $data;
            } elseif ($order->status === 'delivered') {
                $past[] = $data;
            } else {
                $active[] = $data;
            }
        }

        return response()->json([
            'active'    => $active,
            'past'      => $past,
            'cancelled' => $cancelled,
        ]);
    }
}
