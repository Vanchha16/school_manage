<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // ✅ block inactive account
        if (($user->status ?? 1) != 1) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'login' => 'Your account is inactive. Please contact admin.',
            ]);
        }

        $role = strtolower($user->role ?? '');
        $allowed = array_map('strtolower', $roles);

        if (!in_array($role, $allowed, true)) {
            abort(403, 'No permission.');
        }

        return $next($request);
    }
}