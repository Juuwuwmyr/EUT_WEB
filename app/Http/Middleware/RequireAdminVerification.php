<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminVerification
{
    public function handle(Request $request, Closure $next): Response
    {
        // Always require verification on every visit — no TTL/caching
        if (!session('admin_verified_at')) {
            session(['admin_verify_intended' => $request->fullUrl()]);
            return redirect()->route('admin.verify');
        }

        // Clear the flag immediately so next visit requires re-verification
        session()->forget('admin_verified_at');

        return $next($request);
    }
}
