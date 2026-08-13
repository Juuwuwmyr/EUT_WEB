<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailDiagnoseCommand extends Command
{
    protected $signature = 'mail:diagnose {to : Recipient email address}';

    protected $description = 'Show mail config and send plain + verification test emails';

    public function handle(): int
    {
        $to = $this->argument('to');
        $mailer   = config('mail.default');
        $username = config('mail.mailers.smtp.username');
        $from     = config('mail.from.address');

        $this->line('── Mail config ──');
        $this->line('Mailer:  ' . $mailer);
        $this->line('Scheme:  ' . config('mail.mailers.smtp.scheme'));
        $this->line('Host:    ' . config('mail.mailers.smtp.host'));
        $this->line('Port:    ' . config('mail.mailers.smtp.port'));
        $this->line('User:    ' . $username);
        $this->line('From:    ' . $from . ' (' . config('mail.from.name') . ')');
        $this->line('To:      ' . $to);
        $this->newLine();

        if ($mailer === 'log') {
            $this->error('MAIL_MAILER=log — nothing is sent over the network. Fix .env and run: php artisan config:clear');

            return self::FAILURE;
        }

        if ($username && $from && strcasecmp($username, $from) !== 0) {
            $this->warn('MAIL_FROM_ADDRESS differs from MAIL_USERNAME — Gmail may block or spam-filter these.');
        }

        $this->line('1) Sending plain-text test…');
        try {
            Mail::raw(
                "EUT plain test at " . now()->toDateTimeString() . " — if you see this, SMTP delivery works.",
                fn ($message) => $message->to($to)->subject('EUT plain mail test')
            );
            $this->info('   Plain test accepted by SMTP.');
        } catch (\Throwable $e) {
            $this->error('   Plain test failed: ' . $e->getMessage());

            return self::FAILURE;
        }

        $user = User::where('email', $to)->first();
        if ($user) {
            $this->line('2) Sending verification code email…');
            try {
                $user->sendEmailVerificationNotification();
                $this->info('   Verification code email accepted by SMTP.');
            } catch (\Throwable $e) {
                $this->error('   Verification email failed: ' . $e->getMessage());

                return self::FAILURE;
            }
        } else {
            $this->warn('2) Skipped verification test — no user account for ' . $to);
        }

        $this->newLine();
        $this->line('SMTP accepted the message. If inbox is empty:');
        $this->line(' • Check Spam + All Mail on ' . $to);
        $this->line(' • Open gmail.com in a browser (not only the phone app)');
        $this->line(' • Check Sent on ' . ($username ?: 'sender Gmail') . ' — if it appears there, Gmail received it');
        $this->line(' • Search for subject: EUT or E.U.T Snack House');

        return self::SUCCESS;
    }
}
