<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MenuItem extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'featured',
        'is_archived',
        'sort_order',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'featured'    => 'boolean',
        'is_archived' => 'boolean',
        'sort_order'  => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modifierGroups()
    {
        return $this->hasMany(ModifierGroup::class)->orderBy('sort_order');
    }

    public function flavors()
    {
        return $this->hasMany(ModifierGroup::class)->where('type', 'flavor')->orderBy('sort_order');
    }

    public function modifiers()
    {
        return $this->hasMany(ModifierGroup::class)->where('type', 'modifier')->orderBy('sort_order');
    }

    public function addons()
    {
        return $this->hasMany(ModifierGroup::class)->where('type', 'addon')->orderBy('sort_order');
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_archived', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true)->where('is_archived', false);
    }

    public function scopeByCategory(Builder $query, string $slug): Builder
    {
        return $query->whereHas('category', fn($q) => $q->where('slug', $slug));
    }

    // ── Helpers ──────────────────────────────────────────────
    public function isArchived(): bool
    {
        return (bool) $this->is_archived;
    }

    // ── Legacy static helpers (keep dashboard working) ───────
    public static function getAllMenuItems(): array
    {
        return self::with('category')->active()->orderBy('category_id')->orderBy('sort_order')->get()->toArray();
    }

    public static function getFeaturedItems(): array
    {
        return self::with('category')->featured()->get()->toArray();
    }

    public static function getBurgers(): array
    {
        return self::active()->byCategory('burgers')->get()->toArray();
    }

    public static function getSides(): array
    {
        return self::active()->byCategory('sides')->get()->toArray();
    }

    public static function getBeverages(): array
    {
        return self::active()->byCategory('beverages')->get()->toArray();
    }

    public static function getComboMeals(): array
    {
        return self::active()->byCategory('combos')->get()->toArray();
    }

    public static function getItemsByCategory(string $slug): array
    {
        return self::with('category')->active()->byCategory($slug)->orderBy('sort_order')->get()->toArray();
    }
}
