<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'rider_id', 'status',
        'subtotal', 'delivery_fee', 'total',
        'payment_method', 'payment_status',
        'delivery_address', 'delivery_barangay', 'delivery_lat', 'delivery_lng',
        'notes', 'proof_photo', 'delivery_type',
        'cancel_reason',
        'accepted_at', 'prepared_at', 'assigned_at', 'picked_up_at', 'delivered_at', 'cancelled_at',
    ];

    protected $casts = [
        'subtotal'      => 'float',
        'delivery_fee'  => 'float',
        'total'         => 'float',
        'delivery_lat'  => 'float',
        'delivery_lng'  => 'float',
        'accepted_at'   => 'datetime',
        'prepared_at'   => 'datetime',
        'assigned_at'   => 'datetime',
        'picked_up_at'  => 'datetime',
        'delivered_at'  => 'datetime',
        'cancelled_at'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            'pending', 'accepted', 'preparing', 'rider_assigned', 'out_for_delivery'
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Helpers ────────────────────────────────────────────

    public function getOrderNumberAttribute(): string
    {
        return 'EUT-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function isAssignable(): bool
    {
        return in_array($this->status, ['accepted', 'preparing', 'rider_assigned']);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'accepted', 'preparing']);
    }

    public function isPrepared(): bool
    {
        return $this->prepared_at !== null;
    }

    public function scopeKitchenActive($query)
    {
        return $query->whereIn('status', ['pending', 'accepted', 'preparing']);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'          => '#f59e0b',
            'accepted'         => '#3b82f6',
            'preparing'        => '#3b82f6',
            'rider_assigned'   => '#8b5cf6',
            'out_for_delivery' => '#8b5cf6',
            'delivered'        => '#10b981',
            'cancelled'        => '#ef4444',
            default            => '#6b7280',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'          => 'Pending',
            'accepted'         => 'Accepted',
            'preparing'        => 'Preparing',
            'rider_assigned'   => 'Rider Assigned',
            'out_for_delivery' => 'On the Way',
            'delivered'        => 'Delivered',
            'cancelled'        => 'Cancelled',
            default            => ucfirst($this->status),
        };
    }
}
