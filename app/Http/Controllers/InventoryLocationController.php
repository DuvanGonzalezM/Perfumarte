<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\InventoryValidation;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class InventoryLocationController extends Controller
{
    public function start()
    {
        try {
            $user = Auth::user();
            if(!isset($user->location_user[0])){
                return redirect()->route('logout')->with('error', 'El usuario ' . $user->username . ' no tiene una sede asignada');
            }

            $hasAcceptedToday = InventoryValidation::where('location_id', $user->location_user[0]->location_id)
                ->whereDate('date', Carbon::today())
                ->exists();
    
            if ($hasAcceptedToday) {
                return redirect()->route('inventory.current');
            }
            $userLocation = $user->with('location_user.warehouses')->where('user_id', $user->user_id)->first();
            $location_user = $userLocation->location_user;
            $warehouses = $location_user[0]->warehouses;
            $inventory = null;
            if(count($warehouses) > 0){
                $inventory = Inventory::with('product')->where('warehouse_id', $warehouses[0]->warehouse_id)->get();
            }
    
            return Inertia::render('Stock/StartInventoryLocation', [
                'initialInventory' => $inventory,
                'location' => $location_user[0],
                'sidebarHidden' => true
            ]);
        } catch (\Exception $e) {
            return redirect()->route('logout');
        }
    }

    public function accept(Request $request)
    {
        $location = auth()->user()->location_user->first();

        if (! $location) {
            return redirect()->route('dashboard')
                ->with('error', 'Su usuario no tiene una sede asignada.');
        }

        return DB::transaction(function () use ($request, $location) {
            $alreadyAccepted = InventoryValidation::where('location_id', $location->location_id)
                ->whereDate('date', Carbon::today())
                ->lockForUpdate()
                ->exists();

            if ($alreadyAccepted) {
                return redirect()->route('inventory.current');
            }

            InventoryValidation::create([
                'user_id' => auth()->user()->user_id,
                'location_id' => $location->location_id,
                'date' => Carbon::today(),
                'accepted_at' => Carbon::now(),
            ]);

            CashRegister::create([
                'location_id' => $location->location_id,
                'total_collected' => 0,
                'total_digital' => 0,
                'count_100_bill' => $request->count_100_bill ?? 0,
                'count_50_bill' => $request->count_50_bill ?? 0,
                'count_20_bill' => $request->count_20_bill ?? 0,
                'count_10_bill' => $request->count_10_bill ?? 0,
                'count_5_bill' => $request->count_5_bill ?? 0,
                'count_2_bill' => $request->count_2_bill ?? 0,
                'total_coins' => $request->total_coins ?? 0,
            ]);

            return redirect()->route('inventory.current');
        });
    }

    public function current()
    {
        $warehouse = auth()->user()->location_user->first()?->warehouses->first();

        if (! $warehouse) {
            return redirect()->route('dashboard')
                ->with('error', 'Su usuario no tiene una sede con bodega asignada.');
        }

        $inventory = Inventory::with('product')->where('warehouse_id', $warehouse->warehouse_id)->get();

        return Inertia::render('Stock/InventoryLocation', [
            'currentInventory' => $inventory
        ]);
    }
}
