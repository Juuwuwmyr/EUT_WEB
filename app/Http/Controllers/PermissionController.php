<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display the permissions management page
     */
    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        
        $permissionsByGroup = $permissions->groupBy('group');
        
        $roles = ['admin', 'chef', 'rider', 'user'];
        
        // Get permissions for each role
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role] = RolePermission::where('role', $role)
                ->pluck('permission_id')
                ->toArray();
        }

        return view('admin.permissions', compact('permissions', 'permissionsByGroup', 'roles', 'rolePermissions'));
    }

    /**
     * Store a new permission
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'description' => 'nullable|string|max:500',
            'group' => 'required|string|max:100',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'group' => $request->group,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully.',
            'permission' => $permission,
        ]);
    }

    /**
     * Update an existing permission
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'description' => 'nullable|string|max:500',
            'group' => 'required|string|max:100',
        ]);

        $permission->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'group' => $request->group,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
            'permission' => $permission,
        ]);
    }

    /**
     * Delete a permission
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully.',
        ]);
    }

    /**
     * Toggle permission active status
     */
    public function toggleActive(Permission $permission)
    {
        $permission->update([
            'is_active' => !$permission->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission status updated.',
            'is_active' => $permission->is_active,
        ]);
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request)
    {
        $request->validate([
            'role' => 'required|string|in:admin,chef,rider,user',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        RolePermission::syncForRole($request->role, $request->permissions);

        // Clear cache for all users with this role
        User::where('role', $request->role)->each(function ($user) {
            $user->clearPermissionCache();
        });

        return response()->json([
            'success' => true,
            'message' => 'Role permissions updated successfully.',
        ]);
    }

    /**
     * Get user-specific permissions page
     */
    public function userPermissions(User $user)
    {
        $permissions = Permission::where('is_active', true)
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        $permissionsByGroup = $permissions->groupBy('group');

        // Get role permissions
        $rolePermissions = RolePermission::where('role', $user->role ?? 'user')
            ->pluck('permission_id')
            ->toArray();

        // Get user-specific overrides
        $userOverrides = UserPermission::where('user_id', $user->id)
            ->get()
            ->keyBy('permission_id')
            ->map(fn($up) => $up->granted)
            ->toArray();

        return view('admin.user-permissions', compact('user', 'permissions', 'permissionsByGroup', 'rolePermissions', 'userOverrides'));
    }

    /**
     * Update user-specific permissions
     */
    public function updateUserPermissions(Request $request, User $user)
    {
        $request->validate([
            'grant' => 'nullable|array',
            'grant.*' => 'exists:permissions,id',
            'revoke' => 'nullable|array',
            'revoke.*' => 'exists:permissions,id',
            'reset' => 'nullable|array',
            'reset.*' => 'exists:permissions,id',
        ]);

        // Grant permissions
        foreach ($request->input('grant', []) as $permissionId) {
            UserPermission::grant($user->id, $permissionId);
        }

        // Revoke permissions
        foreach ($request->input('revoke', []) as $permissionId) {
            UserPermission::revoke($user->id, $permissionId);
        }

        // Reset to role default (remove override)
        foreach ($request->input('reset', []) as $permissionId) {
            UserPermission::remove($user->id, $permissionId);
        }

        $user->clearPermissionCache();

        return response()->json([
            'success' => true,
            'message' => 'User permissions updated successfully.',
        ]);
    }

    /**
     * Bulk grant permission to a user
     */
    public function grantUserPermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        UserPermission::grant($user->id, $request->permission_id);
        $user->clearPermissionCache();

        return response()->json([
            'success' => true,
            'message' => 'Permission granted to user.',
        ]);
    }

    /**
     * Bulk revoke permission from a user
     */
    public function revokeUserPermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        UserPermission::revoke($user->id, $request->permission_id);
        $user->clearPermissionCache();

        return response()->json([
            'success' => true,
            'message' => 'Permission revoked from user.',
        ]);
    }

    /**
     * Remove user permission override (reset to role default)
     */
    public function resetUserPermission(Request $request, User $user)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        UserPermission::remove($user->id, $request->permission_id);
        $user->clearPermissionCache();

        return response()->json([
            'success' => true,
            'message' => 'Permission reset to role default.',
        ]);
    }

    /**
     * Generate slug from name
     */
    public function generateSlug(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $slug = Str::slug($request->name, '_');

        return response()->json([
            'slug' => $slug,
        ]);
    }
}
