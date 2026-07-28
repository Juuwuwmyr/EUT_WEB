<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'menu_item_id',
        'item_name', 'image', 'unit_price', 'quantity', 'subtotal',
        'modifiers',
    ];

    protected $casts = [
        'unit_price' => 'float',
        'subtotal'   => 'float',
        'modifiers'  => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * Return a fully-qualified image URL.
     * Tries stored image first, then falls back to the live MenuItem image,
     * then a generic placeholder. Handles both full URLs and relative paths.
     */
    public function resolvedImage(): string
    {
        // If stored image is already a full URL (http/https), use it directly
        if ($this->image && str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // If stored image is a relative path, resolve via asset()
        if ($this->image) {
            return asset($this->image);
        }

        // Fall back to the live MenuItem image if the snapshot is missing
        if ($this->menu_item_id) {
            $live = \App\Models\MenuItem::find($this->menu_item_id);
            if ($live && $live->image) {
                return asset($live->image);
            }
        }

        return asset('images/hero-burger.webp');
    }
}
