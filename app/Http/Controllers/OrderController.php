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
            'items.*.qty'      => 'required|integer|min:1',
            'items.*.modifiers'=> 'nullable|array',
            'delivery_address' => 'required|string|max:255',
            'delivery_barangay'=> 'nullable|string|max:100',
            'payment_method'   => 'required|in:cash,gcash,card',
            'notes'            => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $lineItems = [];

            foreach ($request->items as $line) {
                // Cart ID may be composite like "5_12-14" — extract the base menu item ID
                $menuItemId = (int) explode('_', $line['id'])[0];
                $menuItem = MenuItem::findOrFail($menuItemId);
                $qty      = (int) $line['qty'];
                $price    = $menuItem->price;

                // Add modifier price adjustments
                $modifierSummary = [];
                if (!empty($line['modifiers'])) {
                    foreach ($line['modifiers'] as $mod) {
                        $price += (float) ($mod['price_adjustment'] ?? 0);
                        $modifierSummary[] = $mod;
                    }
                }

                $lineSub  = $price * $qty;
                $subtotal += $lineSub;

                $lineItems[] = [
                    'menu_item_id' => $menuItemId,
                    'item_name'    => $menuItem->name,
                    'unit_price'   => $price,
                    'quantity'     => $qty,
                    'subtotal'     => $lineSub,
                    'modifiers'    => $modifierSummary ?: null,
                ];
            }

            $deliveryFee = 50;
            $total       = $subtotal + $deliveryFee;

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
            'id'            => $order->id,
            'order_number'  => $order->order_number,
            'status'        => $order->status,
            'status_label'  => $order->status_label,
            'total'         => $order->total,
            'delivery_address' => $order->delivery_address,
            'payment_method'=> $order->payment_method,
            'placed_at'     => $order->created_at->format('g:i A'),
            'accepted_at'   => $order->accepted_at?->format('g:i A'),
            'picked_up_at'  => $order->picked_up_at?->format('g:i A'),
            'delivered_at'  => $order->delivered_at?->format('g:i A'),
            'rider'         => ($order->rider && $order->rider->user) ? [
                'name'    => $order->rider->user->name,
                'phone'   => $order->rider->phone,
                'rating'  => $order->rider->rating,
                'lat'     => $order->rider->current_lat,
                'lng'     => $order->rider->current_lng,
            ] : null,
            'items' => $order->items->map(fn($i) => [
                'name'     => $i->item_name,
                'qty'      => $i->quantity,
                'price'    => $i->unit_price,
                'subtotal' => $i->subtotal,
            ]),
        ]);
    }

    // ── POST /orders/{order}/cancel ────────────────────────
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
                'payment_method'   => $order->payment_method,
                'notes'            => $order->notes,
                'cancel_reason'    => $order->cancel_reason,
                'placed_at'        => $order->created_at->format('M d, Y g:i A'),
                'accepted_at'      => $order->accepted_at?->format('g:i A'),
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
                'items' => $order->items->map(fn($i) => [
                    'name'      => $i->item_name,
                    'qty'       => $i->quantity,
                    'price'     => $i->unit_price,
                    'subtotal'  => $i->subtotal,
                    'image'     => '/images/hero-burger.jpg', // fallback — ideally store in order_items
                    'modifiers' => $i->modifiers ?? [],
                ]),
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
