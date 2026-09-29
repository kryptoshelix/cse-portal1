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
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
            'active' => \App\Http\Middleware\EnsureActiveAccount::class,
            'guest.dashboard' => \App\Http\Middleware\RedirectAuthenticatedUserToDashboard::class,
        ]);

        // Laravel's built-in `guest` alias only checks the session guard, which
        // is unreliable under `actingAs()` in tests. Replace it with our own
        // role-aware redirect so authenticated users never see login/register.
        $middleware->web(replace: [
            \Illuminate\Auth\Middleware\AuthenticateSession::class ?? '',
        ]);
        $middleware->alias(['guest' => \App\Http\Middleware\RedirectAuthenticatedUserToDashboard::class]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
