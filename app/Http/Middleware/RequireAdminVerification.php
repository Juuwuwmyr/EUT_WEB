<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminVerification
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only gate GET requests (page views) — skip for POST/PUT/PATCH/DELETE (form saves, AJAX)
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Always require verification on every GET visit
        if (!session('admin_verified_at')) {
            session(['admin_verify_intended' => $request->fullUrl()]);
            return redirect()->route('admin.verify');
        }

        // Clear the flag so next page visit requires re-verification
        session()->forget('admin_verified_at');

        return $next($request);
    }
}
