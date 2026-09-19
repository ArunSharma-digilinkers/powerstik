<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards /admin. Usage: `admin` (any active staff) or `admin:editor,sales`
 * (those roles; admins always pass).
 */
class AdminAccess
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('admin.login'));
        }

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('admin.login')->withErrors(['email' => 'This account is disabled.']);
        }

        abort_if($roles && ! $user->hasRole(...$roles), 403);

        return $next($request);
    }
}
