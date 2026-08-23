<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Auditable, HasPermissions;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'avatar',
        'provider',
        'role',
        'email_verified_at',
        'email_verification_code',
        'email_verification_code_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'                  => 'datetime',
            'email_verification_code_expires_at' => 'datetime',
            'password'                           => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRider(): bool
    {
        return $this->role === 'rider';
    }

    public function isChef(): bool
    {
        return $this->role === 'chef';
    }

    public function isWaiter(): bool
    {
        return $this->role === 'waiter';
    }

    public function isGoogleUser(): bool
    {
        return $this->provider === 'google';
    }

    public function rider()
    {
        return $this->hasOne(\App\Models\Rider::class);
    }

    public function addresses()
    {
        return $this->hasMany(\App\Models\UserAddress::class)->orderByDesc('is_default')->orderBy('created_at');
    }

    public function defaultAddress()
    {
        return $this->hasOne(\App\Models\UserAddress::class)->where('is_default', true);
    }

    public function sendEmailVerificationNotification(): void
    {
        $code = $this->issueEmailVerificationCode();
        $this->notify(new \App\Notifications\EmailVerificationCodeNotification($code));
    }

    public function issueEmailVerificationCode(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->updateQuietly([
            'email_verification_code'            => \Illuminate\Support\Facades\Hash::make($code),
            'email_verification_code_expires_at' => now()->addMinutes(15),
        ]);

        return $code;
    }

    public function verifyEmailWithCode(string $code): bool
    {
        if ($this->hasVerifiedEmail()) {
            return true;
        }

        if (! $this->email_verification_code || ! $this->email_verification_code_expires_at) {
            return false;
        }

        if ($this->email_verification_code_expires_at->isPast()) {
            return false;
        }

        if (! \Illuminate\Support\Facades\Hash::check($code, $this->email_verification_code)) {
            return false;
        }

        $this->updateQuietly([
            'email_verified_at'                  => now(),
            'email_verification_code'            => null,
            'email_verification_code_expires_at' => null,
        ]);

        return true;
    }
}
