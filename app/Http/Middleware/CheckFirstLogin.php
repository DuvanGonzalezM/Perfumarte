<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Obliga a cambiar la contraseña predeterminada antes de usar el sistema.
 *
 * Dos correcciones respecto a la versión anterior:
 *
 *  1. Vivía en la pila global $middleware, que Laravel ejecuta ANTES del grupo
 *     'web' y por tanto antes de StartSession: auth()->check() se evaluaba sin
 *     sesión y siempre daba false. Ahora se registra dentro del grupo 'web'.
 *  2. Consultaba la columna `first_login`, que ninguna migración crea. El
 *     estado real está en `default_password`.
 */
class CheckFirstLogin
{
    /**
     * Rutas alcanzables sin haber cambiado aún la contraseña.
     *
     * @var array<int, string>
     */
    private const ALLOWED_ROUTES = [
        'change-password',
        'change-password.update',
        'password.change',
        'password.update',
        'logout',
        'login',
    ];

    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        if (auth()->check()
            && auth()->user()->default_password
            && ! in_array($routeName, self::ALLOWED_ROUTES, true)
        ) {
            return redirect()->route('change-password');
        }

        return $next($request);
    }
}
