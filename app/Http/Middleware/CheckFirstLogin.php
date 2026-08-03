<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFirstLogin
{
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
