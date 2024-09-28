<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Inventory;
use Inertia\Inertia;
use Request;



class DashboardController extends Controller
{

    public function getData()
    {
        $stock1 = Inventory::where('warehouse_id', '=', '1')->get();
        $stock2 = Inventory::where('warehouse_id', '=', '2')->get();
        $stockAlmacen = Inventory::where('warehouse_id', '=', '3')->get();
        $despachos = Dispatch::where('status', '=', 'Finalizado')->get();
        return Inertia::render('Dashboard', ['inventory' => $stock1, 'inventory2' => $stock2, 'inventoryAlmacen' => $stockAlmacen, 'finalized' => $despachos]);

    }

}