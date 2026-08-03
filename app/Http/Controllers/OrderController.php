<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Events\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ── POST /orders — place an order ──────────────────────
    public function store(Request $request)
    {
        // Block ordering when shop is closed
        if (!cache()->get('shop_is_open', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, we are currently closed. Please check back later.',
            ], 422);
        }

        $request->validate([
            'items'            => 'required|array|min:1',            'items.*.id'       => 'required',
            'items.*.qty'      => 'required|integer|min:1|max:99',
            'items.*.modifiers'=> 'nullable|array',
            'order_type'       => 'required|in:delivery,pickup,dine_in',
            'delivery_address' => 'required|string|max:255',
            'delivery_barangay'=> 'nullable|string|max:100',
            'delivery_lat'     => 'nullable|numeric|between:-90,90',
            'delivery_lng'     => 'nullable|numeric|between:-180,180',
            'payment_method'   => 'required|in:cash,gcash,card',
            'notes'            => 'nullable|string|max:500',
            'table_number'     => 'required_if:order_type,dine_in|nullable|string|max:20',
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

            // ── Distance-based delivery fee ─────────────────────────────
            // Formula: ₱30 base (covers first 2 km) + ₱10 per km beyond 2 km.
            // Max range: 100 km. Pickup / dine-in = free.
            $deliveryFee = 0;
            if ($request->order_type === 'delivery') {
                $lat = (float) $request->delivery_lat;
                $lng = (float) $request->delivery_lng;

                if ($lat && $lng) {
                    // Haversine distance from restaurant to customer (in km)
                    $restLat = 13.3213129;
                    $restLng = 121.3027265;
                    $earthR  = 6371;
                    $dLat    = deg2rad($lat - $restLat);
                    $dLng    = deg2rad($lng - $restLng);
                    $a       = sin($dLat/2) * sin($dLat/2)
                             + cos(deg2rad($restLat)) * cos(deg2rad($lat))
                             * sin($dLng/2) * sin($dLng/2);
                    $km      = $earthR * 2 * atan2(sqrt($a), sqrt(1 - $a));

                    if ($km > 100) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Sorry, your location is outside our 100 km delivery range.',
                        ], 422);
                    }

                    // ₱30 for first 2 km, +₱10 per km after that
                    $deliveryFee = 30 + max(0, ceil($km - 2)) * 10;
                } else {
                    // No coordinates yet — use minimum ₱30
                    $deliveryFee = 30;
                }
            }
            $total = round($subtotal + $deliveryFee, 2);

            // ── Dine-in: merge into existing active order for same table ────
            // If the customer already has an active dine-in order at the same table,
            // append items to it and update totals instead of creating a new order.
            if ($request->order_type === 'dine_in' && $request->table_number) {
                $existingOrder = Order::where('user_id', auth()->id())
                    ->where('order_type', 'dine_in')
                    ->where('table_number', $request->table_number)
                    ->whereIn('status', ['pending', 'accepted', 'preparing'])
                    ->latest()
                    ->first();

                if ($existingOrder) {
                    // Append new items to the existing order
                    foreach ($lineItems as $item) {
                        $existingOrder->items()->create($item);
                    }

                    // Recalculate totals from all items
                    $newSubtotal = $existingOrder->items()->sum('subtotal');
                    $existingOrder->update([
                        'subtotal' => round($newSubtotal, 2),
                        'total'    => round($newSubtotal, 2), // dine-in has no delivery fee
                        // Append new notes if any
                        'notes'    => $request->notes
                            ? ($existingOrder->notes
                                ? $existingOrder->notes . ' | ' . $request->notes
                                : $request->notes)
                            : $existingOrder->notes,
                    ]);

                    DB::commit();
                    broadcast(new OrderStatusUpdated($existingOrder))->toOthers();

                    return response()->json([
                        'success'      => true,
                        'order_id'     => $existingOrder->id,
                        'order_number' => $existingOrder->order_number,
                        'total'        => round($newSubtotal, 2),
                        'merged'       => true, // let frontend know it was merged
                        'message'      => 'Items added to your existing table order.',
                    ]);
                }
            }

            $order = Order::create([
                'user_id'          => auth()->id(),
                'status'           => 'pending',
                'order_type'       => $request->order_type,
                'subtotal'         => $subtotal,
                'delivery_fee'     => $deliveryFee,
                'total'            => $total,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'pending',
                'delivery_address' => $request->delivery_address,
                'delivery_barangay'=> $request->delivery_barangay,
                'delivery_lat'     => $request->delivery_lat,
                'delivery_lng'     => $request->delivery_lng,
                'table_number'     => $request->order_type === 'dine_in' ? $request->table_number : null,
                'notes'            => $request->notes,
            ]);

            foreach ($lineItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            broadcast(new OrderStatusUpdated($order))->toOthers();

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
            'order_type'       => $order->order_type,
            'order_type_label' => $order->order_type_label,
            'order_type_icon'  => $order->order_type_icon,
            'status_label'     => $order->status_label,
            'total'            => $order->total,
            'delivery_address' => $order->delivery_address,
            'delivery_lat'     => $order->delivery_lat,
            'delivery_lng'     => $order->delivery_lng,
            'payment_method'   => $order->payment_method,
            'table_number'     => $order->table_number,
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
                'image'     => $i->resolvedImage(),
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

        broadcast(new OrderStatusUpdated($order));

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
                'order_type'       => $order->order_type,
                'order_type_label' => $order->order_type_label,
                'order_type_icon'  => $order->order_type_icon,
                'status_label'     => $order->status_label,
                'subtotal'         => $order->subtotal,
                'delivery_fee'     => $order->delivery_fee,
                'total'            => $order->total,
                'delivery_address' => $order->delivery_address,
                'delivery_lat'     => $order->delivery_lat,
                'delivery_lng'     => $order->delivery_lng,
                'payment_method'   => $order->payment_method,
                'table_number'     => $order->table_number,
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
                        'image'     => $i->resolvedImage(),
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

    // ── GET /delivery-fee — calculate fee from coordinates ─────────────
    public function calcFee(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat    = (float) $request->lat;
        $lng    = (float) $request->lng;
        $restLat = 13.3213129;
        $restLng = 121.3027265;
        $earthR  = 6371;

        $dLat = deg2rad($lat - $restLat);
        $dLng = deg2rad($lng - $restLng);
        $a    = sin($dLat/2) * sin($dLat/2)
              + cos(deg2rad($restLat)) * cos(deg2rad($lat))
              * sin($dLng/2) * sin($dLng/2);
        $km   = round($earthR * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);

        if ($km > 100) {
            return response()->json([
                'success' => false,
                'message' => 'Outside delivery range (max 100 km).',
                'km'      => $km,
            ], 422);
        }

        // ₱30 for first 2 km, +₱10 per km after that (rounded up per km)
        $fee = 30 + max(0, ceil($km - 2)) * 10;

        return response()->json([
            'success' => true,
            'km'      => $km,
            'fee'     => $fee,
            'label'   => $km <= 2
                ? "₱{$fee} (within 2 km)"
                : "₱{$fee} (" . number_format($km, 1) . " km)",
        ]);
    }
}
