<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Railway (like most PaaS hosts) puts the app behind a reverse
        // proxy that terminates TLS. Without this, Laravel can't reliably
        // tell the request was HTTPS or see the real client IP, which
        // affects secure-cookie handling and anything that inspects
        // $request->ip() or $request->isSecure(). Trusting all proxies is
        // safe here because Railway's network sits in front of every
        // request the app receives; the app is never reachable directly.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'standard.user' => \App\Http\Middleware\EnsureUserIsStandardUser::class,
        ]);

        // Send guests hitting a protected route to the login page instead
        // of Laravel's default /login (which we are overriding anyway).
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
