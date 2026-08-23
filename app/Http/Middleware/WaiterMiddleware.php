<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WaiterMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('home')->with('error', 'Please log in.');
        }

        $user = auth()->user();

        if (! $user->isWaiter() && ! $user->isAdmin()) {
            abort(403, 'Access denied. Waiters only.');
        }

        return $next($request);
    }
}
