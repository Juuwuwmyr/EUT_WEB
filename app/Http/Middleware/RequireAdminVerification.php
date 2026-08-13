<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminVerification
{
    // How long (seconds) the verification stays valid — 30 minutes
    const TTL = 1800;

    public function handle(Request $request, Closure $next): Response
    {
        $verifiedAt = session('admin_verified_at');

        if (!$verifiedAt || (time() - $verifiedAt) > self::TTL) {
            // Store the intended URL so we redirect back after verification
            session(['admin_verify_intended' => $request->fullUrl()]);
            return redirect()->route('admin.verify');
        }

        return $next($request);
    }
}
