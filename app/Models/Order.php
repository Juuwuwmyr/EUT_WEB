<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'rider_id', 'status', 'order_type',
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

    public function getOrderTypeLabelAttribute(): string
    {
        return match($this->order_type) {
            'delivery' => 'Delivery',
            'pickup'   => 'Pickup',
            'dine_in'  => 'Dine-in',
            default    => ucfirst($this->order_type),
        };
    }

    public function getOrderTypeIconAttribute(): string
    {
        return match($this->order_type) {
            'delivery' => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3M5 8c0 1.657 3.134 3 7 3s7-1.343 7-3"/><circle cx="17" cy="17" r="2"/><path stroke-linecap="round" d="M3 11h3l1.5 5h9l1.5-5h3"/></svg>',
            'pickup'   => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
            'dine_in'  => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 19h6"/></svg>',
            default    => '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>',
        };
    }
}
