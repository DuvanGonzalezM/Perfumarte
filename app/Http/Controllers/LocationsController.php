<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Location;
use App\Models\Zone;

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


}   
