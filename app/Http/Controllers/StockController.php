<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function getStocks()
    {
        $warehouses = Warehouse::whereHas('location.userLocation', function ($query) {
            return $query->where('user_id', '=', session('user_id'));
        })->get();
        return Inertia::render('Stock/Dashboard', ['warehouses' => $warehouses]);
    }
    
    public function getAllInventory($warehouse_id)
    {
        $inventory = $this->getInventory($warehouse_id);
        return Inertia::render('Stock/Inventory', ['inventory' => $inventory['inventory'], 'warehouse' => $inventory['warehouse']]);
    }

    public function getMultipleInventory()
    {
        $locations = Location::with('warehouses')->whereDoesntHave('userLocation', function ($query) {
            return $query->where('user_id', '=', session('user_id'));
        })->get();
        return Inertia::render('Stock/InventoryMultiple', ['locations' => $locations]);
    }

    public function getInventory($warehouse_id)
    {
        $warehouse = Warehouse::findOrFail($warehouse_id);
        $inventory = Inventory::with('product.supplier')->where('warehouse_id', '=', $warehouse_id)->get();
        $response = [
            'warehouse' => $warehouse,
            'inventory' => $inventory
        ];
        return $response;
    }
}
