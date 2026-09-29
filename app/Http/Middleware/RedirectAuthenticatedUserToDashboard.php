<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Already-authenticated users must never see the login/register pages —
 * they are sent to their own role-appropriate dashboard (never a shared
 * "admin" landing for everyone).
 */
class RedirectAuthenticatedUserToDashboard
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return redirect(UserRole::dashboardRoute($request->user()) ?? route('home'));
        }

        return $next($request);
    }
}
