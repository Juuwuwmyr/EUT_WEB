<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Clears verification for any protected area the admin is not currently on.
 * Leaving Dashboard / Categories / Menu requires re-verification on return.
 */
class SyncAdminVerificationScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $currentScope = $this->resolveScope($request);

        foreach (RequireAdminVerification::SCOPES as $scope) {
            if ($scope !== $currentScope) {
                session()->forget(RequireAdminVerification::sessionKey($scope));
            }
        }

        return $next($request);
    }

    private function resolveScope(Request $request): ?string
    {
        $name = $request->route()?->getName() ?? '';

        if (in_array($name, ['admin.verify', 'admin.verify.submit'], true)) {
            $pending = session('admin_verify_scope');

            return in_array($pending, RequireAdminVerification::SCOPES, true) ? $pending : null;
        }

        if ($name === 'admin.dashboard') {
            return 'dashboard';
        }

        if (str_starts_with($name, 'admin.categories')) {
            return 'categories';
        }

        if (
            str_starts_with($name, 'admin.menu-items')
            || str_starts_with($name, 'admin.modifier-groups')
            || str_starts_with($name, 'admin.modifier-options')
        ) {
            return 'menu';
        }

        return null;
    }
}
