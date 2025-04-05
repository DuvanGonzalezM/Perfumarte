<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Location;
use App\Models\Zone;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\LocationUser;
use App\Models\Role;
use App\Models\CashRegister;
use App\Models\Sale;


class LocationsController extends Controller
{
    public function getLocations(Request $request)
    {
        $zones = Zone::all();
        $locations = Location::with('zone')->get();
        return Inertia::render('Locations/LocationsList', [
            'locations' => $locations, 
            'zones' => $zones
        ]);
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'zone_id' => 'required',
            'cash_base' => 'required',
        ]);

        Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'zone_id' => $request->zone_id,
            'cash_base' => $request->cash_base
        ]);
        
        return redirect()->route('locations.list');
    }

    public function destroyLocation($id)
    {
        Location::destroy($id);
        return redirect()->route('locations.list');
    }

    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'zone_id' => 'required',
            'cash_base' => 'required',
        ]);
        

        $location = Location::findOrFail($id);
        $location->update($request->all());

        return redirect()->route('locations.list');
    }

    public function detailLocation($id)
    {

        return Inertia::render('Locations/LocationsDetail', [
            'location' => Location::with('zone')->findOrFail($id)
        ]);


    }

    public function inventorylocation($id){
        $location = Location::with('zone')->findOrFail($id);
        
        $currentInventory = Inventory::with('product')
            ->whereHas('warehouse', function ($query) use ($id) {
                $query->where('location_id', $id);
            })
            ->get()
            ->map(function ($inventory) {
                return [
                    'product' => $inventory->product,
                    'quantity' => $inventory->quantity,
                    'measurement_unit' => $inventory->product->measurement_unit ?? 'ml'
                ];
            });
    
        return Inertia::render('Locations/LocationsInventory', [
            'location' => $location,
            'currentInventory' => $currentInventory
        ]);


    }

    public function personallocation($id){
        $location = Location::with(['zone', 'users_location'])->findOrFail($id);
        
        return Inertia::render('Locations/LocationsStaff', [
            'location' => $location,
            'users' => $location->users_location
        ]);
    }

    public function saleslocation($id){
        $location = Location::with('zone')->findOrFail($id);
        
        // Obtener todas las ventas relacionadas con las cajas registradoras de esta ubicación
        $cashRegisters = CashRegister::where('location_id', $id)->get();
    
        return Inertia::render('Locations/LocationsSales', [
            'location' => $location,
            'cashRegisters' => $cashRegisters
        ]);
    }

}
