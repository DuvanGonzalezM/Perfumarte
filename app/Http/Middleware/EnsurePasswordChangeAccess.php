<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordChangeAccess
{
    public function handle(Request $request, Closure $next)
    {
        $username = (string) $request->route('username');

        if ($request->hasValidSignature()) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->username === $username) {
            return $next($request);
        }

        abort(403, 'El enlace de cambio de contraseña no es válido o ya venció.');
    }
}
