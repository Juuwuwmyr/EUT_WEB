<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminVerification
{
    /** Verification window in seconds (must match verify page copy). */
    private const TTL = 30 * 60;

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isVerified()) {
            return $next($request);
        }

        session(['admin_verify_intended' => $request->fullUrl()]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => false,
                'message'  => 'Password verification required.',
                'redirect' => route('admin.verify'),
            ], 403);
        }

        return redirect()->route('admin.verify');
    }

    private function isVerified(): bool
    {
        $verifiedAt = session('admin_verified_at');

        if (! $verifiedAt) {
            return false;
        }

        if ((time() - (int) $verifiedAt) > self::TTL) {
            session()->forget('admin_verified_at');

            return false;
        }

        return true;
    }
}
