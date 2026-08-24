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
        $orderType      = $request->input('order_type', 'delivery');
        $isOpenDelivery = cache()->get('shop_is_open_delivery', true);
        $isOpenPickup   = cache()->get('shop_is_open_pickup', true);
        $isOpenDineIn   = cache()->get('shop_is_open_dine_in', true);

        if ($orderType === 'delivery' && !$isOpenDelivery) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Delivery service is currently closed. Please choose another option or check back later.',
            ], 422);
        }

        if ($orderType === 'pickup' && !$isOpenPickup) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Pickup service is currently closed. Please choose another option or check back later.',
            ], 422);
        }

        if ($orderType === 'dine_in' && !$isOpenDineIn) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, Dine-In service is currently closed. Please check back later.',
            ], 422);
        }

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

        // Staff (waiter/chef/admin/rider) taking a dine-in order on a customer's
        // behalf must NOT be attributed as the order owner — otherwise the staff
        // member's name replaces "Guest" on kitchen/waiter dashboards, the order
        // pollutes their own order history, and they receive the customer's
        // order-confirmation email on every order they place for a table.
        $isStaffPlacingDineIn = $isDineIn && auth()->check() && in_array(auth()->user()->role, ['admin', 'waiter', 'chef', 'rider'], true);

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
                $menuItem   = MenuItem::find($menuItemId);

                if (!$menuItem) {
                    DB::rollBack();
                    // Clear the stale cart item — item no longer exists on this server
                    return response()->json([
                        'success'       => false,
                        'message'       => 'One or more items in your cart are no longer available. Please refresh the page and try again.',
                        'stale_item_id' => $menuItemId,
                        'clear_cart'    => true,
                    ], 422);
                }

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

            // ── Barangay-based flat delivery fee ─────────────────────────────
            // Uses exact fee from naujan_barangays config per barangay.
            // Falls back to distance calculation only if barangay not found in config.
            $deliveryFee = 0;
            if ($request->order_type === 'delivery') {
                // First: try to get flat fee from barangay config
                $barangays   = config('naujan_barangays', []);
                $barangay    = $request->delivery_barangay ?? '';
                
                if ($barangay && isset($barangays[$barangay])) {
                    // Use exact barangay fee from config
                    $deliveryFee = $barangays[$barangay];
                } else {
                    // Fallback: calculate from GPS distance if barangay not in config
                    $lat = (float) $request->delivery_lat;
                    $lng = (float) $request->delivery_lng;

                    // Use barangay center coords if GPS not provided
                    if (!$lat || !$lng) {
                        $barangayCoords = config('naujan_barangay_coords', []);
                        if ($barangay && isset($barangayCoords[$barangay])) {
                            $lat = $barangayCoords[$barangay][0];
                            $lng = $barangayCoords[$barangay][1];
                        }
                    }

                    if ($lat && $lng) {
                        $restLat = 13.321512;
                        $restLng = 121.302098;
                        $earthR  = 6371;
                        $dLat    = deg2rad($lat - $restLat);
                        $dLng    = deg2rad($lng - $restLng);
                        $a       = sin($dLat/2) * sin($dLat/2)
                                 + cos(deg2rad($restLat)) * cos(deg2rad($lat))
                                 * sin($dLng/2) * sin($dLng/2);
                        $km      = $earthR * 2 * atan2(sqrt($a), sqrt(1 - $a));

                        // ₱30 for first 2 km, +₱10 per km after that
                        $deliveryFee = 30 + max(0, ceil($km - 2)) * 10;
                    } else {
                        // No barangay config and no coords — default to ₱50
                        $deliveryFee = 50;
                    }
                }
            }
            $total = round($subtotal + $deliveryFee, 2);

            // ── Dine-in merge rules ──────────────────────────────────────────────
            //  pending  → merge (admin hasn't accepted yet, safe to add items)
            //  accepted → merge (still in queue, chef hasn't started, safe to add)
            //  preparing → NEW independent order (chef is actively cooking — never
            //               touch a cooking order; new items get their own queue
            //               entry, their own accept step, and their own receipt)
            //  accepted/preparing → NEW independent order (admin has already accepted,
            //               don't disrupt the existing queue entry; new items get
            //               their own accept step and their own kitchen ticket)
            //  delivered/cancelled → NEW order (session already closed)
            if ($request->order_type === 'dine_in' && $request->table_number) {
                // Only merge into pending — once accepted or cooking, always new order.
                // Also skip merge if the session was locked by admin (Done Ordering).
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
                    // Append new items to the existing order
                    foreach ($lineItems as $item) {
                        $existingOrder->items()->create($item);
                    }

                    // Recalculate totals from all items
                    $newSubtotal = $existingOrder->items()->sum('subtotal');

                    $updateData = [
                        'subtotal' => round($newSubtotal, 2),
                        'total'    => round($newSubtotal, 2),
                        'notes'    => $request->notes
                            ? ($existingOrder->notes
                                ? $existingOrder->notes . ' | ' . $request->notes
                                : $request->notes)
                            : $existingOrder->notes,
                    ];

                    $existingOrder->update($updateData);
                    $existingOrder->refresh(); // reload so broadcast carries updated status

                    DB::commit();
                    try { broadcast(new OrderStatusUpdated($existingOrder))->toOthers(); } catch (\Throwable $be) { \Log::warning('Broadcast failed (merge): ' . $be->getMessage()); }

                    return response()->json([
                        'success'      => true,
                        'order_id'     => $existingOrder->id,
                        'order_number' => $existingOrder->order_number,
                        'total'        => round($newSubtotal, 2),
                        'merged'       => true,
                        'message'      => 'Items added to your table order.',
                    ]);
                }
                // No pending/unlocked order for this table — chef is already cooking,
                // session is done, or admin locked ordering. Fall through to create a fresh order.
            }

            // For brand-new table sessions (no pending/accepted order to merge into),
            // inherit the session ID from any active, UNLOCKED order at this table so
            // all orders from the same sitting appear together on the final receipt.
            // If the active session is locked (admin marked Done Ordering), generate a
            // brand-new UUID so this order starts a fresh session — this also protects
            // against a new customer accidentally joining a previous customer's bill.
            $tableSessionId = null;
            if ($request->order_type === 'dine_in' && $request->table_number) {
                // Inherit the session ID from any still-open order at this table today
                // (unlocked and not yet paid). Once payment completes the session is
                // locked, so the next customer always gets a brand-new table group.
                $activeSession = Order::where('order_type', 'dine_in')
                    ->where('table_number', $request->table_number)
                    ->when(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'ordering_locked'),
                        fn($q) => $q->where('ordering_locked', false)
                    )
                    ->when(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'payment_status'),
                        fn($q) => $q->where('payment_status', '!=', 'paid')
                    )
                    ->whereDate('created_at', today())
                    ->whereNotNull('table_session_id')
                    ->latest()
                    ->value('table_session_id');

                $tableSessionId = $activeSession ?? \Illuminate\Support\Str::uuid();
            }

            $order = Order::create([
                'user_id'          => $isStaffPlacingDineIn ? null : auth()->id(), // null for dine-in guests / staff-placed orders
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
                // Reuse the existing session ID if this table already has an active order,
                // so all orders from the same sitting appear on one combined table receipt.
                'table_session_id' => $tableSessionId,
            ]);

            foreach ($lineItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            try { broadcast(new OrderStatusUpdated($order))->toOthers(); } catch (\Throwable $be) { \Log::warning('Broadcast failed (new order): ' . $be->getMessage()); }

            // Send order confirmation email to logged-in users (skip for staff
            // placing an order on a customer's behalf — they shouldn't get it)
            if (auth()->check() && auth()->user()->email && !$isStaffPlacingDineIn) {
                try {
                    auth()->user()->notify(new \App\Notifications\OrderConfirmationNotification($order));
                } catch (\Throwable $me) {
                    \Log::warning('Order confirmation email failed: ' . $me->getMessage());
                }
            }

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
            'subtotal'         => $order->subtotal,
            'delivery_fee'     => $order->delivery_fee,
            'total'            => $order->total,
            'delivery_address' => $order->delivery_address,
            'delivery_lat'     => $order->delivery_lat,
            'delivery_lng'     => $order->delivery_lng,
            'payment_method'   => $order->payment_method,
            'payment_status'   => $order->payment_status,
            'cash_received'    => $order->cash_received,
            'change_due'       => $order->change_due,
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

        try { broadcast(new OrderStatusUpdated($order)); } catch (\Throwable $be) { \Log::warning('Broadcast failed (cancel): ' . $be->getMessage()); }

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
                'payment_status'   => $order->payment_status,
                'cash_received'    => $order->cash_received,
                'change_due'       => $order->change_due,
                'table_number'     => $order->table_number,
                'notes'            => $order->notes,
                'cancel_reason'    => $order->cancel_reason,
                'placed_at'        => $order->created_at->format('M d, Y g:i A'),
                'created_at_ts'    => $order->created_at->timestamp,
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

    // ── GET /orders/table/{table} — public: dine-in guests check their table order ──
    public function tableStatus(string $table)
    {
        // Sanitise: table number must be 1-2 digits
        if (!preg_match('/^\d{1,2}$/', $table)) {
            return response()->json(['order' => null]);
        }

        $order = Order::with('items')
            ->where('order_type', 'dine_in')
            ->where('table_number', $table)
            ->whereNotIn('status', ['cancelled'])
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        if (!$order) {
            return response()->json(['order' => null]);
        }

        $statusLabels = [
            'pending'   => ['label' => 'Order Received',    'color' => '#f59e0b', 'icon' => '⏳'],
            'accepted'  => ['label' => 'Accepted',          'color' => '#3b82f6', 'icon' => '✅'],
            'preparing' => ['label' => 'Being Prepared',    'color' => '#8b5cf6', 'icon' => '👨‍🍳'],
            'ready'     => ['label' => 'Ready to Serve',    'color' => '#10b981', 'icon' => '🍽️'],
            'delivered' => ['label' => 'Served',            'color' => '#4ade80', 'icon' => '🎉'],
        ];

        $cfg = $statusLabels[$order->status] ?? $statusLabels['pending'];

        return response()->json([
            'order' => [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'status'       => $order->status,
                'status_label' => $cfg['label'],
                'status_color' => $cfg['color'],
                'status_icon'  => $cfg['icon'],
                'table_number' => $order->table_number,
                'total'        => $order->total,
                'placed_at'    => $order->created_at->format('g:i A'),
                'items'        => $order->items->map(fn($i) => [
                    'name'     => $i->item_name,
                    'qty'      => $i->quantity,
                    'subtotal' => $i->subtotal,
                ]),
            ],
        ]);
    }

    // ── GET /delivery-fee — calculate fee by distance from shop ──────────
    public function calcFee(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat     = (float) $request->lat;
        $lng     = (float) $request->lng;
        $restLat = 13.321512;
        $restLng = 121.302098;
        $earthR  = 6371;

        $dLat = deg2rad($lat - $restLat);
        $dLng = deg2rad($lng - $restLng);
        $a    = sin($dLat/2) * sin($dLat/2)
              + cos(deg2rad($restLat)) * cos(deg2rad($lat))
              * sin($dLng/2) * sin($dLng/2);
        $km   = round($earthR * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);

        // ₱30 for first 2 km, +₱10 per km after that
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
