<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Auditable;

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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
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
}
