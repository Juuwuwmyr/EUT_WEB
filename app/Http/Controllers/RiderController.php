<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RiderController extends Controller
{
    // ── GET /rider/dashboard ───────────────────────────────
    public function dashboard()
    {
        $rider  = auth()->user()->rider;
        $active = Order::with(['user', 'items'])
            ->where('rider_id', $rider->id)
            ->whereIn('status', ['rider_assigned', 'out_for_delivery'])
            ->latest()
            ->get();

        $history = Order::with(['items'])
            ->where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->latest('delivered_at')
            ->get();

        $todayDeliveries = $history->count();
        $todayEarnings   = $todayDeliveries * 120; // ₱120 per delivery (configurable)

        return view('rider.dashboard', compact('rider', 'active', 'history', 'todayDeliveries', 'todayEarnings'));
    }

    // ── PATCH /rider/status — go online/offline ────────────
    public function updateStatus(Request $request)
    {
        $request->validate(['is_available' => 'required|boolean']);
        $rider = auth()->user()->rider;
        $rider->update(['is_available' => $request->is_available]);
        return response()->json(['success' => true, 'is_available' => $rider->is_available]);
    }

    // ── PATCH /rider/location — GPS ping ──────────────────
    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        auth()->user()->rider->update([
            'current_lat' => $request->lat,
            'current_lng' => $request->lng,
        ]);

        return response()->json(['success' => true]);
    }

    // ── POST /rider/orders/{order}/picked-up ──────────────
    public function pickedUp(Order $order)
    {
        $rider = auth()->user()->rider;
        $this->authorizeRiderOrder($order, $rider);

        if ($order->status !== 'rider_assigned') {
            return response()->json(['success' => false, 'message' => 'Invalid status transition.'], 422);
        }

        $order->update([
            'status'      => 'out_for_delivery',
            'picked_up_at'=> now(),
        ]);

        return response()->json(['success' => true, 'status' => 'out_for_delivery']);
    }

    // ── POST /rider/orders/{order}/delivered ──────────────
    public function delivered(Request $request, Order $order)
    {
        $rider = auth()->user()->rider;
        $this->authorizeRiderOrder($order, $rider);

        if ($order->status !== 'out_for_delivery') {
            return response()->json(['success' => false, 'message' => 'Invalid status transition.'], 422);
        }

        $photoPath = null;
        if ($request->hasFile('proof_photo')) {
            $photoPath = $request->file('proof_photo')
                ->store('proof_photos/' . $order->id, 'public');
        }

        $order->update([
            'status'        => 'delivered',
            'delivered_at'  => now(),
            'proof_photo'   => $photoPath,
            // 'present' (direct handover) maps to DB enum value 'handover'
            'delivery_type' => $request->input('delivery_type') === 'photo' ? 'photo' : 'handover',
        ]);

        // Increment rider stats
        $rider->increment('total_deliveries');

        return response()->json(['success' => true, 'status' => 'delivered']);
    }

    // ── GET /rider/orders — active orders ─────────────────
    public function orders()
    {
        $rider  = auth()->user()->rider;
        $orders = Order::with(['user', 'items'])
            ->where('rider_id', $rider->id)
            ->whereIn('status', ['rider_assigned', 'out_for_delivery'])
            ->latest()
            ->get()
            ->map(fn($o) => [
                'id'               => $o->id,
                'order_number'     => $o->order_number,
                'status'           => $o->status,
                'customer_name'    => $o->user->name,
                'customer_phone'   => $o->user->phone ?? '',
                'delivery_address' => $o->delivery_address,
                'delivery_lat'     => $o->delivery_lat,
                'delivery_lng'     => $o->delivery_lng,
                'total'            => $o->total,
                'items_summary'    => $o->items->map(fn($i) => $i->item_name . ' × ' . $i->quantity)->implode(' · '),
                'assigned_at'      => $o->assigned_at?->format('g:i A'),
            ]);

        return response()->json($orders);
    }

    // ── GET /rider/earnings ────────────────────────────────
    public function earnings()
    {
        $rider = auth()->user()->rider;

        $today = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count();

        $week = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $month = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->whereMonth('delivered_at', now()->month)
            ->whereYear('delivered_at', now()->year)
            ->count();

        $ratePerDelivery = 120; // ₱120

        return response()->json([
            'today_deliveries' => $today,
            'today_earnings'   => $today * $ratePerDelivery,
            'week_deliveries'  => $week,
            'week_earnings'    => $week * $ratePerDelivery,
            'month_deliveries' => $month,
            'month_earnings'   => $month * $ratePerDelivery,
            'all_time'         => $rider->total_deliveries,
            'rating'           => $rider->rating,
        ]);
    }

    // ── Private ────────────────────────────────────────────
    private function authorizeRiderOrder(Order $order, Rider $rider): void
    {
        if ($order->rider_id !== $rider->id) {
            abort(403, 'This order is not assigned to you.');
        }
    }
}
