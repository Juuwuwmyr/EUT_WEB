<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShopStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public bool $isOpen) {}

    public function broadcastOn(): array
    {
        // Public channel — all visitors can receive this without auth
        return [new Channel('shop.status')];
    }

    public function broadcastAs(): string
    {
        return 'shop.status';
    }

    public function broadcastWith(): array
    {
        return ['is_open' => $this->isOpen];
    }
}
