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

        // ── Naujan-only restriction ─────────────────────────────────────────
        // Skip for dine-in — they're already physically at the restaurant.
        $isDineIn = $request->input('order_type') === 'dine_in';

        if (!$isDineIn) {
            $cLat = (float) $request->input('customer_lat', 0);
            $cLng = (float) $request->input('customer_lng', 0);
            if ($cLat && $cLng) {
                $naujanLat = 13.3215;
                $naujanLng = 121.3021;
                $earthR    = 6371;
                $dLat      = deg2rad($cLat - $naujanLat);
                $dLng      = deg2rad($cLng - $naujanLng);
                $a         = sin($dLat/2) * sin($dLat/2)
                           + cos(deg2rad($naujanLat)) * cos(deg2rad($cLat))
                           * sin($dLng/2) * sin($dLng/2);
                $distKm    = $earthR * 2 * atan2(sqrt($a), sqrt(1 - $a));

                if ($distKm > 30) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sorry, this service is exclusive to customers within Naujan, Oriental Mindoro. Your location appears to be outside our coverage area.',
                        'outside_naujan' => true,
                    ], 422);
                }
            }
        }

        // Guests can only place dine-in orders
        if (!auth()->check() && !$isDineIn) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to place a delivery or pickup order.',
            ], 401);
        }

        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required',
            'items.*.qty'      => 'required|integer|min:1|max:99',
            'items.*.modifiers'=> 'nullable|array',
            'order_type'       => 'required|in:delivery,pickup,dine_in',
            'delivery_address' => 'required_unless:order_type,dine_in|nullable|string|max:255',
            'delivery_barangay'=> 'nullable|string|max:100',
            'delivery_lat'     => 'nullable|numeric|between:-90,90',
            'delivery_lng'     => 'nullable|numeric|between:-180,180',
            'payment_method'   => 'nullable|in:cash,gcash,card',
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

            // ── Barangay-based flat delivery fee ────────────────────────────
            // Fee is determined by the customer's barangay, not GPS distance.
            // Pickup / dine-in = free.
            $deliveryFee = 0;
            if ($request->order_type === 'delivery') {
                $barangay  = $request->delivery_barangay ?? '';
                $barangays = config('naujan_barangays', []);
                $deliveryFee = $barangays[$barangay] ?? 50; // ₱50 default if unknown
            }
            $total = round($subtotal + $deliveryFee, 2);

            // ── Dine-in: merge into existing active order for same table ────
            // Always merge new items into the active table order (same table number, any user).
            // If the order was already accepted/preparing, reset it to 'pending' so the
            // admin must re-accept and the kitchen gets a new print for the added items.
            if ($request->order_type === 'dine_in' && $request->table_number) {
                $existingOrder = Order::where('order_type', 'dine_in')
                    ->where('table_number', $request->table_number)
                    ->whereIn('status', ['pending', 'accepted', 'preparing'])
                    ->whereDate('created_at', today())
                    ->latest()
                    ->first();

                if ($existingOrder) {
                    // Append new items to the existing order
                    foreach ($lineItems as $item) {
                        $existingOrder->items()->create($item);
                    }

                    // Recalculate totals from all items
                    $newSubtotal = $existingOrder->items()->sum('subtotal');

                    $updateData = [
                        'subtotal' => round($newSubtotal, 2),
                        'total'    => round($newSubtotal, 2), // dine-in has no delivery fee
                        'notes'    => $request->notes
                            ? ($existingOrder->notes
                                ? $existingOrder->notes . ' | ' . $request->notes
                                : $request->notes)
                            : $existingOrder->notes,
                    ];

                    // If already in kitchen (accepted/preparing), push back to pending
                    // so admin must re-accept — this triggers a new kitchen ticket print
                    if (in_array($existingOrder->status, ['accepted', 'preparing'])) {
                        $updateData['status']      = 'pending';
                        $updateData['accepted_at'] = null;
                        $updateData['prepared_at'] = null;
                    }

                    $existingOrder->update($updateData);

                    DB::commit();
                    broadcast(new OrderStatusUpdated($existingOrder))->toOthers();

                    return response()->json([
                        'success'      => true,
                        'order_id'     => $existingOrder->id,
                        'order_number' => $existingOrder->order_number,
                        'total'        => round($newSubtotal, 2),
                        'merged'       => true,
                        'message'      => 'Items added to your table order.',
                    ]);
                }
            }

            $order = Order::create([
                'user_id'          => auth()->id(), // null for dine-in guests
                'status'           => 'pending',
                'order_type'       => $request->order_type,
                'subtotal'         => $subtotal,
                'delivery_fee'     => $deliveryFee,
                'total'            => $total,
                'payment_method'   => $request->payment_method ?? 'cash',
                'payment_status'   => 'pending',
                'delivery_address' => $request->delivery_address ?? ('Dine-in · Table ' . $request->table_number),
                'delivery_barangay'=> $request->delivery_barangay,
                'delivery_lat'     => $request->delivery_lat,
                'delivery_lng'     => $request->delivery_lng,
                'table_number'     => $request->order_type === 'dine_in' ? $request->table_number : null,
                'notes'            => $request->notes,
                // Each new dine-in sitting gets a unique session ID so the receipt
                // query only groups orders from the same customer session, never
                // bleeding into a previous session at the same table on the same day.
                'table_session_id' => $request->order_type === 'dine_in' ? \Illuminate\Support\Str::uuid() : null,
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

    // ── GET /delivery-fee — get fee by barangay name ─────────────────────
    public function calcFee(Request $request)
    {
        $request->validate([
            'barangay' => 'required|string|max:100',
        ]);

        $barangays   = config('naujan_barangays', []);
        $barangay    = $request->barangay;
        $fee         = $barangays[$barangay] ?? 50; // ₱50 default if not in list
        $inList      = array_key_exists($barangay, $barangays);

        return response()->json([
            'success'  => true,
            'fee'      => $fee,
            'barangay' => $barangay,
            'label'    => "₱{$fee} flat rate — {$barangay}",
            'in_list'  => $inList,
        ]);
    }
}
