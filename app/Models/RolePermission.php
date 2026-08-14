<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    protected $fillable = [
        'role',
        'permission_id',
    ];

    /**
     * Get the permission that this role has
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Get all permissions for a role
     */
    public static function forRole(string $role): array
    {
        return static::where('role', $role)
            ->with('permission')
            ->get()
            ->pluck('permission.slug')
            ->toArray();
    }

    /**
     * Sync permissions for a role
     */
    public static function syncForRole(string $role, array $permissionIds): void
    {
        // Remove all existing permissions for this role
        static::where('role', $role)->delete();

        // Add new permissions
        foreach ($permissionIds as $permissionId) {
            static::create([
                'role' => $role,
                'permission_id' => $permissionId,
            ]);
        }
    }
}
