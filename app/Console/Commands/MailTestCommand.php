<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailTestCommand extends Command
{
    protected $signature = 'mail:test {to? : Recipient email address}';

    protected $description = 'Send a test email using the current MAIL_* .env settings';

    public function handle(): int
    {
        $to = $this->argument('to') ?: config('mail.from.address');

        if (! $to) {
            $this->error('No recipient. Pass an email or set MAIL_FROM_ADDRESS in .env');

            return self::FAILURE;
        }

        $mailer = config('mail.default');

        $this->line('Mailer:  ' . $mailer);
        $this->line('Host:    ' . config('mail.mailers.smtp.host'));
        $this->line('Port:    ' . config('mail.mailers.smtp.port'));
        $this->line('User:    ' . config('mail.mailers.smtp.username'));
        $this->line('From:    ' . config('mail.from.address') . ' (' . config('mail.from.name') . ')');
        $this->line('To:      ' . $to);
        $this->newLine();

        if ($mailer === 'log') {
            $this->warn('MAIL_MAILER is still "log". Update .env to smtp and run: php artisan config:clear');

            return self::FAILURE;
        }

        try {
            Mail::raw(
                'EUT SMTP test — if you received this, mail is working.',
                fn ($message) => $message->to($to)->subject('EUT Mail Test')
            );

            $this->info('Test email sent successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to send: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
