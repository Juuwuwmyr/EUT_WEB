<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'menu_item_id',
        'cart_key',
        'item_name',
        'image',
        'price',
        'quantity',
        'category',
        'modifiers',
    ];

    protected $casts = [
        'modifiers' => 'array',
        'price'     => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
