<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\InventoryValidation;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;

class InventoryLocationController extends Controller
{
    public function start()
    {
        $user = Auth::user();
        
        $hasAcceptedToday = $user->inventoryValidations()
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
            $inventory = Inventory::with(relations: 'product')->where('warehouse_id', $warehouses[0]->warehouse_id)->get();
        }

        return Inertia::render('Stock/StartInventoryLocation', [
            'initialInventory' => $inventory,
            'location' => $location_user[0],
            'sidebarHidden' => true
        ]);
    }

    public function accept(Request $request)
    {
        $user = Auth::user();
        InventoryValidation::create([
            'user_id' => $user->user_id,
            'location_id' => $user->location_id,
            'date' => Carbon::today(),
            'accepted_at' => Carbon::now(),
        ]);

        CashRegister::create([
            'location_id' => $user->location_id,
            'total_collected' => 0,
            'count_100_bill' => 0,
            'count_50_bill' => $request->count_50_bill ?? 0,
            'count_20_bill' => $request->count_20_bill ?? 0,
            'count_10_bill' => $request->count_10_bill ?? 0,
            'count_5_bill' => $request->count_5_bill ?? 0,
            'count_2_bill' => $request->count_2_bill ?? 0,
            'count_1_bill' => $request->count_1_bill ?? 0,
            'total_coins' => $request->total_coins ?? 0,
        ]);

        return redirect()->route('inventory.current');
    }

    public function current()
    {
        $user = Auth::user();

        $warehouses = $user->location->with('warehouses')->where('location_id', $user->location_id)->first();
        $inventory = Inventory::with('product')->where('warehouse_id', $warehouses->warehouses[0]->warehouse_id)->get();

        return Inertia::render('Stock/InventoryLocation', [
            'currentInventory' => $inventory
        ]);
    }
}
