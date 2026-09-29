<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->status !== AccountStatus::Active) {
            // Pending/rejected/deactivated users cannot touch protected areas.
            if ($user->status === AccountStatus::Pending) {
                return redirect()->route('account.pending');
            }

            // Deactivated/rejected: log out with a safe message (no detail leak).
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'This account is not available. Please contact the department office.',
            ]);
        }

        return $next($request);
    }
}
