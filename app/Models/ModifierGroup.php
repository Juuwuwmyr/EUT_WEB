<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModifierGroup extends Model
{
    protected $fillable = [
        'menu_item_id',
        'type',
        'name',
        'description',
        'required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'required'   => 'boolean',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function options()
    {
        return $this->hasMany(ModifierOption::class)->orderBy('sort_order');
    }

    public function activeOptions()
    {
        return $this->hasMany(ModifierOption::class)->where('is_active', true)->orderBy('sort_order');
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopeFlavors(Builder $query): Builder
    {
        return $query->where('type', 'flavor');
    }

    public function scopeModifiers(Builder $query): Builder
    {
        return $query->where('type', 'modifier');
    }

    public function scopeAddons(Builder $query): Builder
    {
        return $query->where('type', 'addon');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ──────────────────────────────────────────────
    public function isFlavor(): bool   { return $this->type === 'flavor'; }
    public function isModifier(): bool { return $this->type === 'modifier'; }
    public function isAddon(): bool    { return $this->type === 'addon'; }
}
