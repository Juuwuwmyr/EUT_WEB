<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MailVerifyTestCommand extends Command
{
    protected $signature = 'mail:verify-test {to : Recipient email address}';

    protected $description = 'Send a real signup verification email to test SMTP delivery';

    public function handle(): int
    {
        $to = $this->argument('to');
        $mailer = config('mail.default');

        $this->line('Mailer: ' . $mailer);
        $this->line('To:     ' . $to);
        $this->newLine();

        if ($mailer === 'log') {
            $this->error('MAIL_MAILER is "log". Emails are NOT sent — only logged. Fix .env and run: php artisan config:clear');

            return self::FAILURE;
        }

        $user = User::where('email', $to)->first();

        if (! $user) {
            $this->error('No account with that email. Sign up first, or use: php artisan mail:test ' . $to);

            return self::FAILURE;
        }

        try {
            $user->sendEmailVerificationNotification();
            $this->info('Verification code email sent. Check inbox AND spam for: ' . $to);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
