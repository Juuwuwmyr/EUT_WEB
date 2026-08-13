<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

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
            ->subject('Your E.U.T Snack House verification code')
            ->greeting('Welcome to E.U.T Snack House!')
            ->line('Thanks for signing up. Enter this code on the verification page to activate your account:')
            ->line(new HtmlString(
                '<p style="font-size:36px;font-weight:700;letter-spacing:10px;text-align:center;margin:28px 0;color:#111;">'
                . e($this->code)
                . '</p>'
            ))
            ->line('This code expires in 15 minutes.')
            ->line('If you did not create an account, you can safely ignore this email.');
    }
}
