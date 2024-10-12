<?php

namespace App\Http\Controllers;

use App\Models\ChangeWarehouse;
use App\Models\Inventory;
use Inertia\Inertia;
use Illuminate\Http\Request;

class RepackageController extends Controller
{

    public function getrepackage()
    {

        $repackageList = ChangeWarehouse::with(['inventory.product',])->get();
        return Inertia::render('Repackage/RepackageList', props: ['repackageList' => $repackageList]);
    }

    public function createRepackage()
    {

        $productsId = ['16', '17'];

        $getProduct = Inventory::with('product')->where('warehouse_id', '=', '1')->whereIn('product_id', $productsId)->get();

        return Inertia::render('Repackage/CreateRepackage', ['getProduct' => $getProduct]);
    }

    public function storeRepackage(request $request)
    {

        $warehouse = '2';

        $request->validate([
            'reference' => 'required',
            'quantity' => 'required|numeric',
        ]);

        $inventory = Inventory::where('warehouse_id', '=', $warehouse)->where('product_id', '=', $request['reference'])->first();

        $inventoryOut = Inventory::where('warehouse_id', '=', '1')->where('product_id', '=', $request['reference'])->first();

        if ($inventory) {
            $quantity = $inventory->quantity + $request['quantity'];
            $inventory->update([
                'quantity' => $quantity
            ]);
        } else {
            $inventory = Inventory::create([
                'warehouse_id' => $warehouse,
                'product_id' => $request['reference'],
                'quantity' => $request['quantity']
            ]);
        }

        $quantity = $inventoryOut->quantity - $request['quantity'];
        $inventoryOut->update([
            'quantity' => $quantity
        ]);

        ChangeWarehouse::create([
            'change_warehouse_id' => $request->change_warehouse_id,
            'inventory_id' => $inventory->inventory_id,
            'quantity' => $request['quantity'],
        ]);

        return redirect()->route('repackage.list', ['message' => '', 'status' => 200]);

    }
}