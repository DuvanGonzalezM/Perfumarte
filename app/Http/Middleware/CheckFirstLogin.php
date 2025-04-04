<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFirstLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->first_login && !in_array($request->route()->getName(), ['change-password'])) {
            return redirect()->route('change-password');
        }

        return $next($request);
    }
}
