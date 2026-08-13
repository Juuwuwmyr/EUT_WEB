<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCodeNotification extends Notification
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
            ->subject("E.U.T Snack House code: {$this->code}")
            ->greeting('Welcome to E.U.T Snack House!')
            ->line('Your email verification code is:')
            ->line($this->code)
            ->line('Enter this 6-digit code on the verification page to activate your account.')
            ->line('This code expires in 15 minutes.')
            ->line('If you did not create an account, you can safely ignore this email.');
    }
}
