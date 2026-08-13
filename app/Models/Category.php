<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use Auditable;
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'is_archived',
        'sort_order',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'sort_order'  => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function activeMenuItems()
    {
        return $this->hasMany(MenuItem::class)->where('is_archived', false);
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

    // ── Helpers ──────────────────────────────────────────────
    public function isArchived(): bool
    {
        return (bool) $this->is_archived;
    }
}
