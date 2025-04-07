<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckInventoryAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user->hasRole('Asesor comercial')) {
            $hasAcceptedToday = $user->inventoryValidations()
                ->whereDate('date', Carbon::today())
                ->exists();

            if (!$hasAcceptedToday) {
                return redirect()->route('inventory.start');
            }
        }

        return $next($request);
    }
}
