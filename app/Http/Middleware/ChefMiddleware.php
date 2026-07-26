<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChefMiddleware
{
    /**
     * Handle an incoming request.
     * Allows access only to authenticated users whose role is 'chef' or 'admin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('home')->with('error', 'Please log in to access the kitchen panel.');
        }

        if (! auth()->user()->isChef() && ! auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Chefs only.');
        }

        return $next($request);
    }
}
