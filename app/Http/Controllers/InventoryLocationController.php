<?php

namespace App\Http\Controllers;

use App\Models\InventoryValidation;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Warehouse;
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

        $warehouses = $user->location->with('warehouses')->where('location_id', $user->location_id)->first();
        $inventory = Inventory::with('product')->where('warehouse_id', $warehouses->warehouses[0]->warehouse_id)->get();
        return Inertia::render('Stock/StartInventoryLocation', [
            'initialInventory' => $inventory,
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
