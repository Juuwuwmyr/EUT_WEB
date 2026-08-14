<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'group',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all roles that have this permission
     */
    public function roles(): array
    {
        return RolePermission::where('permission_id', $this->id)
            ->pluck('role')
            ->toArray();
    }

    /**
     * Get users who have this permission explicitly granted/revoked
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions')
            ->withPivot('granted')
            ->withTimestamps();
    }

    /**
     * Assign this permission to a role
     */
    public function assignToRole(string $role): void
    {
        RolePermission::firstOrCreate([
            'role' => $role,
            'permission_id' => $this->id,
        ]);
    }

    /**
     * Remove this permission from a role
     */
    public function removeFromRole(string $role): void
    {
        RolePermission::where('role', $role)
            ->where('permission_id', $this->id)
            ->delete();
    }

    /**
     * Check if a role has this permission
     */
    public function hasRole(string $role): bool
    {
        return RolePermission::where('role', $role)
            ->where('permission_id', $this->id)
            ->exists();
    }

    /**
     * Get permissions by group
     */
    public static function byGroup(): array
    {
        return static::where('is_active', true)
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group')
            ->toArray();
    }

    /**
     * Get all available groups
     */
    public static function groups(): array
    {
        return static::distinct()
            ->pluck('group')
            ->sort()
            ->values()
            ->toArray();
    }
}
