<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  The permission slug required
     * @param  string|null  $operator  'any' or 'all' when checking multiple permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!auth()->check()) {
            return $this->unauthorized($request);
        }

        $user = auth()->user();

        // If no permissions specified, just check if authenticated
        if (empty($permissions)) {
            return $next($request);
        }

        // Check for operator (any/all) if multiple permissions
        $operator = 'all'; // default
        if (count($permissions) > 1 && in_array(end($permissions), ['any', 'all'])) {
            $operator = array_pop($permissions);
        }

        // Check permissions
        $hasPermission = $operator === 'any'
            ? $user->hasAnyPermission($permissions)
            : $user->hasAllPermissions($permissions);

        if (!$hasPermission) {
            return $this->unauthorized($request, $permissions);
        }

        return $next($request);
    }

    /**
     * Handle unauthorized access
     */
    private function unauthorized(Request $request, array $permissions = []): Response
    {
        $permissionList = !empty($permissions) ? implode(', ', $permissions) : 'this resource';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access ' . $permissionList . '.',
                'required_permissions' => $permissions,
            ], 403);
        }

        return redirect()
            ->back()
            ->with('error', 'You do not have permission to access this resource.');
    }
}
