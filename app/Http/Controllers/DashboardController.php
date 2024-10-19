<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Inventory;
use App\Models\Request;
use App\Models\RequestPrais;
use Inertia\Inertia;




class DashboardController extends Controller
{
    public function getDataInventory()
    {
        $stock1 = Inventory::where('warehouse_id', '1')->get();
        $stock2 = Inventory::where('warehouse_id', '2')->get();
        $stockAlmacen = Inventory::where('warehouse_id', '3')->get();
        $despachos = Dispatch::where('status', 'Finalizado')->get();
        $despachosPendientes = Dispatch::where('status', 'en ruta')->get();
        $transformacionPendiente = RequestPrais::where('request_type', '2')->where('status', 'Pendiente')->get();
        return Inertia::render('Dashboard', ['inventory' => $stock1, 'inventory2' => $stock2, 'inventoryAlmacen' => $stockAlmacen, 'finalized' => $despachos,'pendingDispatches' => $despachosPendientes,'pendingTransformation' => $transformacionPendiente]);

    }
}