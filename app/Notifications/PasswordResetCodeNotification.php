<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetCodeNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("E.U.T Snack House — Password Reset Code: {$this->code}")
            ->view('emails.password-reset-code', [
                'code' => $this->code,
                'name' => $notifiable->name ?? 'there',
            ]);
    }
}
