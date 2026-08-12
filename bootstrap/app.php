<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (tunnels like ngrok/Cloudflare) so X-Forwarded-Proto
        // is honored and asset URLs are generated as https (no mixed-content CSS/JS).
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
            | Request::HEADER_X_FORWARDED_AWS_ELB);

        $middleware->alias([
            'privacy.lock' => \App\Http\Middleware\EnsurePrivacyUnlocked::class,
            'owner.only' => \App\Http\Middleware\EnsureOwner::class,
        ]);

        // Re-lock the PIN whenever the user navigates out of the sensitive sections,
        // so Diary/Recovery ask for the PIN on every fresh entry.
        $middleware->web(append: [
            \App\Http\Middleware\RelockPrivacyWhenLeaving::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
