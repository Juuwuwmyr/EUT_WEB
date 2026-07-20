<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'address',
        'barangay',
        'city',
        'postal',
        'is_default',
        'lat',
        'lng',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'lat'        => 'float',
        'lng'        => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Full formatted address string */
    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->barangay, $this->city, $this->postal])
            ->filter()
            ->implode(', ');
    }
}
