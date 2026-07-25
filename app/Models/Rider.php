<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rider extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'vehicle_type',
        'plate_number',
        'is_available',
        'current_lat',
        'current_lng',
        'rating',
        'total_deliveries',
    ];

    protected $casts = [
        'is_available'  => 'boolean',
        'current_lat'   => 'float',
        'current_lng'   => 'float',
        'rating'        => 'float',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activeOrder()
    {
        return $this->orders()
            ->whereIn('status', ['rider_assigned', 'out_for_delivery'])
            ->latest()
            ->first();
    }

    // ── Helpers ────────────────────────────────────────────

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? 'Unknown';
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->user?->name ?? 'R');
        return strtoupper(implode('', array_map(fn($p) => $p[0], array_slice($parts, 0, 2))));
    }

    public function getStatusLabelAttribute(): string
    {
        // Always check active order first — a rider on delivery is "on_delivery"
        // regardless of their is_available flag
        if ($this->activeOrder()) return 'on_delivery';
        if (!$this->is_available) return 'offline';
        return 'online';
    }
}
