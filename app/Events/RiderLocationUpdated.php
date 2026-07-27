<?php

namespace App\Events;

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
        return [
            new PrivateChannel('admin.riders'),
        ];
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
