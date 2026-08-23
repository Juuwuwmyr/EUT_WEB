<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("E.U.T Snack House — Order #{$this->order->order_number} Confirmed! 🎉")
            ->view('emails.order-confirmation', [
                'order' => $this->order->load('items'),
                'name'  => $notifiable->name ?? 'there',
            ]);
    }
}
