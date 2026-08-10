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

    public function __construct(
        public bool $isOpen,
        public bool $isOpenDelivery = true,
        public bool $isOpenPickup = true,
        public bool $isOpenDineIn = true,
    ) {}

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
        return [
            'is_open'          => $this->isOpen,
            'is_open_delivery' => $this->isOpenDelivery,
            'is_open_pickup'   => $this->isOpenPickup,
            'is_open_dine_in'  => $this->isOpenDineIn,
        ];
    }
}
