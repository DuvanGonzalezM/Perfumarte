<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use App\Models\InventoryValidation;

class CheckInventoryAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if($user->enabled == 0) {
            return redirect()->route('logout');
        }
        if ($user->hasRole('Asesor comercial')) {
            $location = $user->location_user->first();

            if (! $location) {
                return redirect()->route('logout')
                    ->with('error', 'El usuario '.$user->username.' no tiene una sede asignada');
            }

            $hasAcceptedToday = InventoryValidation::where('location_id', $location->location_id)
                ->whereDate('date', Carbon::today())
                ->exists();

            if (!$hasAcceptedToday) {
                return redirect()->route('inventory.start');
            }
        }
        return $next($request);
    }
}
