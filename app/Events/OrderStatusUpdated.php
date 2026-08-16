<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $payload;

    public function __construct(public Order $order)
    {
        $order->loadMissing(['items', 'rider.user']);

        $this->payload = [
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
            'notes'            => $order->notes,
            'cancel_reason'    => $order->cancel_reason,
            'placed_at'        => $order->created_at->format('M d, Y g:i A'),
            'accepted_at'      => $order->accepted_at?->format('g:i A'),
            'assigned_at'      => $order->assigned_at?->format('g:i A'),
            'picked_up_at'     => $order->picked_up_at?->format('g:i A'),
            'delivered_at'     => $order->delivered_at?->format('g:i A'),
            'cancelled_at'     => $order->cancelled_at?->format('g:i A'),
            'rider'            => ($order->rider && $order->rider->user) ? [
                'name'   => $order->rider->user->name,
                'phone'  => $order->rider->phone,
                'rating' => $order->rider->rating,
                'lat'    => $order->rider->current_lat,
                'lng'    => $order->rider->current_lng,
            ] : null,
            'items' => $order->items->map(fn($i) => [
                'name'      => $i->item_name,
                'qty'       => $i->quantity,
                'price'     => $i->unit_price,
                'subtotal'  => $i->subtotal,
                'image'     => $i->image ? asset($i->image) : asset('images/hero-burger.webp'),
                'modifiers' => $i->modifiers ?? [],
            ])->all(),
        ];
    }

    /**
     * Channels this event broadcasts on:
     *  - private customer channel (shop tracking page) — only when there is an authenticated user
     *  - private kitchen channel (chef dashboard)
     *  - private admin orders channel (admin panel)
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('kitchen'),
            new PrivateChannel('admin.orders'),
        ];

        // Guest dine-in orders have no user_id — skip the customer channel
        // to avoid broadcasting to an invalid 'orders.' channel name
        if ($this->order->user_id) {
            $channels[] = new PrivateChannel('orders.' . $this->order->user_id);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'order.updated';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
