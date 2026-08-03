<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PrintServerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = config('app.print_server_token');
        $provided = $request->header('X-Print-Token') ?? $request->query('token');

        if (!$token || $provided !== $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
