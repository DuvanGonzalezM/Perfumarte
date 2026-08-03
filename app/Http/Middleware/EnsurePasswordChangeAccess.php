<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Protege las rutas de cambio de contraseña de usuarios con contraseña
 * predeterminada.
 *
 * Solo se permite el acceso por dos vías:
 *
 *  1. Un enlace de activación firmado y con vencimiento, que emite un
 *     administrador al crear, reactivar o restablecer la cuenta.
 *  2. El propio usuario autenticado, sobre su propio username.
 *
 * Cualquier otro acceso se rechaza. Antes, estas rutas estaban bajo el
 * middleware 'guest' sin ninguna comprobación, de modo que quien conociera un
 * username en estado default_password podía fijarle la contraseña.
 */
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
