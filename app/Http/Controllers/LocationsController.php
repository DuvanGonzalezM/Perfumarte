<?php

namespace App\Http\Controllers;

use App\Models\Audit;
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
        $locations = Location::with('zone', 'warehouses')->whereNot('location_id', 1)->get();
        return Inertia::render('Locations/LocationsList', [
            'locations' => $locations, 
            'zones' => $zones,
        ]);
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'zone_id' => 'required',
            'cash_base' => 'required',
            'price30' => 'required',
            'price50' => 'required',
            'price100' => 'required',
        ]);

        $location = Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'zone_id' => $request->zone_id,
            'cash_base' => $request->cash_base
        ]);

        $warehouse = Warehouse::create([
            'location_id' => $location->location_id,
            'name' => $request->name,
            'price30' => $request->price30,
            'price50' => $request->price50,
            'price100' => $request->price100,
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
            'price30' => 'required',
            'price50' => 'required',
            'price100' => 'required',
        ]);
       
        

        $location = Location::findOrFail($id);
        $location->update($request->all());
        $warehouse = Warehouse::where('location_id', $id)->first();
        $warehouse->update([
            'price30' => $request->price30,
            'price50' => $request->price50,
            'price100' => $request->price100,
        ]);

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
        $location = Location::with(['zone', 'users_location.roles'])->findOrFail($id);
        
        return Inertia::render('Locations/LocationsStaff', [
            'location' => $location,
            'users' => $location->users_location
        ]);
    }

    public function saleslocation($id){
        $location = Location::with('zone')->findOrFail($id);
        
        $cashRegisters = CashRegister::where('location_id', $id)->get();
    
        return Inertia::render('Locations/LocationsSales', [
            'location' => $location,
            'cashRegisters' => $cashRegisters
        ]);
    }

    public function salesDetail($location_id, $cash_register_id)
    {
        $location = Location::with('zone')->findOrFail($location_id);
        
        $cashRegister = CashRegister::with([
            'sales' => function($query) {
                $query->with([
                    'saleDetails' => function($q) {
                        $q->with('inventory.product');
                    },
                    'user'
                ])
                ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($cash_register_id);

        $sales = $cashRegister->sales->map(function($sale) {
            return [
                'id' => $sale->sale_id,
                'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'transaction_code' => $sale->transaction_code,
                'user' => $sale->user->name,
                'details' => $sale->saleDetails->map(function($detail) {
                    return [
                        'product' => $detail->inventory->product->name,
                        'reference' => $detail->inventory->product->reference,
                        'quantity' => $detail->quantity,
                        'units' => $detail->units,
                        'drops' => $detail->drops,
                        'price' => $detail->price,
                        'total' => $detail->quantity * $detail->price
                    ];
                })
            ];
        });

        return Inertia::render('Locations/LocationsSalesDetail', [
            'location' => $location,
            'cashRegister' => $cashRegister,
            'sales' => $sales
        ]);
    }

    public function auditlocation($location_id)
    {
        $audits = Audit::with('location', 'user')->where('location_id', $location_id)->get();
        $location = Location::findOrFail($location_id);
        return Inertia::render('Locations/LocationsAudit', [
            'audits'=> $audits,
            'location' => $location
        ]);
    }
}
