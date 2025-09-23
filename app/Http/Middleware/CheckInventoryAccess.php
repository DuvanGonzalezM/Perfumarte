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
            $hasAcceptedToday = InventoryValidation::where('location_id', $user->location_user[0]->location_id)
                ->whereDate('date', Carbon::today())
                ->exists();

            if (!$hasAcceptedToday) {
                return redirect()->route('inventory.start');
            }
        }
        return $next($request);
    }
}
