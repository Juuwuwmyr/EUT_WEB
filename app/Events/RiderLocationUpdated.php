<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Rider;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiderLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Rider $rider) {}

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('admin.riders')];

        // Also broadcast to customers who have an active delivery with this rider
        $activeOrders = Order::where('rider_id', $this->rider->id)
            ->whereIn('status', ['rider_assigned', 'out_for_delivery'])
            ->pluck('user_id');

        foreach ($activeOrders as $userId) {
            $channels[] = new PrivateChannel('orders.' . $userId);
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'rider.location';
    }

    public function broadcastWith(): array
    {
        return [
            'id'     => $this->rider->id,
            'name'   => $this->rider->user?->name,
            'lat'    => $this->rider->current_lat,
            'lng'    => $this->rider->current_lng,
            'status' => $this->rider->status,
        ];
    }
}
