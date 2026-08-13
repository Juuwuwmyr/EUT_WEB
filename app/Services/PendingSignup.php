<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PendingSignup
{
    public const SESSION_KEY = 'pending_signup';

    public static function put(array $data, string $plainCode): void
    {
        session([
            self::SESSION_KEY => [
                'name'       => trim($data['name']),
                'email'      => $data['email'],
                'phone'      => $data['phone'],
                'password'   => Hash::make($data['password']),
                'code_hash'  => Hash::make($plainCode),
                'expires_at' => now()->addMinutes(15)->timestamp,
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
        session()->forget(self::SESSION_KEY);
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
        $pending['expires_at']   = now()->addMinutes(15)->timestamp;

        session([self::SESSION_KEY => $pending]);
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
