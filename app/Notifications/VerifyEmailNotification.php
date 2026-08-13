<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify your E.U.T Snack House account')
            ->greeting('Welcome to E.U.T Snack House!')
            ->line('Thanks for signing up. Please confirm your email address to activate your account and start ordering.')
            ->action('Verify Email Address', $url)
            ->line('This link expires in 60 minutes.')
            ->line('If you did not create an account, you can safely ignore this email.');
    }
}
