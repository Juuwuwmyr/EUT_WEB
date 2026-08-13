<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PendingSignup
{
    public const SESSION_KEY = 'pending_signup';

    public const RESEND_SESSION_KEY = 'verification_code_sent_at';

    public const RESEND_COOLDOWN_SECONDS = 60;

    public static function put(array $data, string $plainCode): void
    {
        session([
            self::SESSION_KEY => [
                'name'         => trim($data['name']),
                'email'        => $data['email'],
                'phone'        => $data['phone'],
                'password'     => Hash::make($data['password']),
                'code_hash'    => Hash::make($plainCode),
                'expires_at'   => now()->addMinutes(15)->timestamp,
                'code_sent_at' => now()->timestamp,
            ],
        ]);
    }

    public static function get(): ?array
    {
        return session(self::SESSION_KEY);
    }

    public static function email(): ?string
    {
        return self::get()['email'] ?? null;
    }

    public static function forget(): void
    {
        session()->forget([self::SESSION_KEY, self::RESEND_SESSION_KEY]);
    }

    public static function has(): bool
    {
        return self::get() !== null;
    }

    public static function isExpired(?array $pending = null): bool
    {
        $pending ??= self::get();

        if (! $pending) {
            return true;
        }

        return now()->timestamp > ($pending['expires_at'] ?? 0);
    }

    public static function verifyCode(string $code): bool
    {
        $pending = self::get();

        if (! $pending || self::isExpired($pending)) {
            return false;
        }

        return Hash::check($code, $pending['code_hash']);
    }

    public static function refreshCode(string $plainCode): void
    {
        $pending = self::get();

        if (! $pending) {
            return;
        }

        $pending['code_hash']  = Hash::make($plainCode);
        $pending['expires_at'] = now()->addMinutes(15)->timestamp;

        session([self::SESSION_KEY => $pending]);
    }

    public static function markCodeSent(): void
    {
        $pending = self::get();

        if ($pending) {
            $pending['code_sent_at'] = now()->timestamp;
            session([self::SESSION_KEY => $pending]);

            return;
        }

        session([self::RESEND_SESSION_KEY => now()->timestamp]);
    }

    public static function resendCooldownRemaining(): int
    {
        $sentAt = self::get()['code_sent_at'] ?? session(self::RESEND_SESSION_KEY);

        if (! $sentAt) {
            return 0;
        }

        $elapsed = now()->timestamp - (int) $sentAt;

        return max(0, self::RESEND_COOLDOWN_SECONDS - $elapsed);
    }

    public static function canResendCode(): bool
    {
        return self::resendCooldownRemaining() === 0;
    }

    public static function sendCodeEmail(string $email, string $name, string $code): void
    {
        if (config('mail.default') === 'log') {
            throw new \RuntimeException('MAIL_MAILER is log — emails are written to the log file only.');
        }

        Mail::send('emails.verification-code', [
            'code' => $code,
            'name' => $name,
        ], function ($message) use ($email, $code) {
            $message->to($email)
                ->subject("E.U.T Snack House — Verification code: {$code}");
        });
    }

    public static function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
