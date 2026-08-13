<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminVerification
{
    /** Verification window in seconds (must match verify page copy). */
    private const TTL = 30 * 60;

    public const SCOPES = ['dashboard', 'categories', 'menu'];

    public function handle(Request $request, Closure $next, string $scope = 'menu'): Response
    {
        if (! in_array($scope, self::SCOPES, true)) {
            $scope = 'menu';
        }

        if (static::isVerified($scope)) {
            return $next($request);
        }

        session([
            'admin_verify_intended' => $request->fullUrl(),
            'admin_verify_scope'    => $scope,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => false,
                'message'  => 'Password verification required.',
                'redirect' => route('admin.verify'),
            ], 403);
        }

        return redirect()->route('admin.verify');
    }

    public static function sessionKey(string $scope): string
    {
        return 'admin_verified_' . $scope . '_at';
    }

    public static function isVerified(string $scope): bool
    {
        if (! in_array($scope, self::SCOPES, true)) {
            return false;
        }

        $key        = static::sessionKey($scope);
        $verifiedAt = session($key);

        if (! $verifiedAt) {
            return false;
        }

        if ((time() - (int) $verifiedAt) > self::TTL) {
            session()->forget($key);

            return false;
        }

        return true;
    }

    public static function markVerified(string $scope): void
    {
        if (in_array($scope, self::SCOPES, true)) {
            session([static::sessionKey($scope) => time()]);
        }
    }

    public static function scopeLabel(string $scope): string
    {
        return match ($scope) {
            'dashboard'  => 'Dashboard',
            'categories' => 'Categories',
            'menu'       => 'Menu Items',
            default      => 'this area',
        };
    }
}
