<?php

namespace App\Models\Concerns;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Cache;

trait HasPermissions
{
    /**
     * Get all permissions for this user (both role-based and user-specific)
     */
    public function permissions(): array
    {
        $cacheKey = "user_permissions_{$this->id}";

        return Cache::remember($cacheKey, 3600, function () {
            // Get role-based permissions
            $rolePermissions = RolePermission::forRole($this->role ?? 'user');

            // Get user-specific permissions
            $userPermissions = UserPermission::where('user_id', $this->id)
                ->with('permission')
                ->get();

            // Start with role permissions
            $permissions = collect($rolePermissions);

            // Apply user-specific overrides
            foreach ($userPermissions as $userPerm) {
                if ($userPerm->granted) {
                    // Grant additional permission
                    if (!$permissions->contains($userPerm->permission->slug)) {
                        $permissions->push($userPerm->permission->slug);
                    }
                } else {
                    // Revoke permission
                    $permissions = $permissions->reject(fn($slug) => $slug === $userPerm->permission->slug);
                }
            }

            return $permissions->toArray();
        });
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Super admin bypass (optional)
        if ($this->role === 'admin' && $this->email === env('SUPER_ADMIN_EMAIL')) {
            return true;
        }

        return in_array($permissionSlug, $this->permissions());
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Grant a permission to this user
     */
    public function grantPermission(string|int $permission): void
    {
        $permissionId = is_string($permission)
            ? Permission::where('slug', $permission)->first()?->id
            : $permission;

        if ($permissionId) {
            UserPermission::grant($this->id, $permissionId);
            $this->clearPermissionCache();
        }
    }

    /**
     * Revoke a permission from this user
     */
    public function revokePermission(string|int $permission): void
    {
        $permissionId = is_string($permission)
            ? Permission::where('slug', $permission)->first()?->id
            : $permission;

        if ($permissionId) {
            UserPermission::revoke($this->id, $permissionId);
            $this->clearPermissionCache();
        }
    }

    /**
     * Remove user-specific permission override (revert to role default)
     */
    public function removePermissionOverride(string|int $permission): void
    {
        $permissionId = is_string($permission)
            ? Permission::where('slug', $permission)->first()?->id
            : $permission;

        if ($permissionId) {
            UserPermission::remove($this->id, $permissionId);
            $this->clearPermissionCache();
        }
    }

    /**
     * Get user-specific permission overrides
     */
    public function permissionOverrides(): array
    {
        return UserPermission::where('user_id', $this->id)
            ->with('permission')
            ->get()
            ->map(fn($up) => [
                'permission_id' => $up->permission_id,
                'slug' => $up->permission->slug,
                'name' => $up->permission->name,
                'granted' => $up->granted,
            ])
            ->toArray();
    }

    /**
     * Clear permission cache for this user
     */
    public function clearPermissionCache(): void
    {
        Cache::forget("user_permissions_{$this->id}");
    }

    /**
     * Get relationship to user-specific permissions
     */
    public function userPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withPivot('granted')
            ->withTimestamps();
    }

    /**
     * Check if user cannot access a feature
     */
    public function cannotAccess(string $permissionSlug): bool
    {
        return !$this->hasPermission($permissionSlug);
    }
}
