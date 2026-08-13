<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin'            => \App\Http\Middleware\AdminMiddleware::class,
            'rider'            => \App\Http\Middleware\RiderMiddleware::class,
            'chef'             => \App\Http\Middleware\ChefMiddleware::class,
            'auth.printserver' => \App\Http\Middleware\PrintServerMiddleware::class,
            'admin.verify'     => \App\Http\Middleware\RequireAdminVerification::class,
            'admin.sync-verify'=> \App\Http\Middleware\SyncAdminVerificationScope::class,
        ]);

        // This project uses a modal-based login on the home page,
        // so redirect unauthenticated users there instead of route('login').
        $middleware->redirectGuestsTo(fn () => route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
