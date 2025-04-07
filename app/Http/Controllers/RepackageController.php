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

        $getProduct = Inventory::with('product')->where('warehouse_id', '1')->whereIn('product_id', $productsId)->get();

        return Inertia::render('Repackage/CreateRepackage', ['getProduct' => $getProduct]);
    }

    public function storeRepackage(request $request)
    {

        $warehouse = '2';

        $request->validate([
            'reference' => 'required',
            'quantity' => 'required|numeric',
        ]);

        $inventory = Inventory::where('warehouse_id', $warehouse)->where('product_id', $request['reference'])->first();

        $inventoryOut = Inventory::where('warehouse_id', '1')->where('product_id', $request['reference'])->first();

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
            'inventory_id' => $inventory->inventory_id,
            'quantity' => $request['quantity'],
        ]);

        return redirect()->route('repackage.list');

    }

    public function editRepackage($repackageId)
    {
        $inventory = Inventory::with('product')->where('warehouse_id', 2)->get();
        $repackage = ChangeWarehouse::with('inventory.product')->where('change_warehouse_id', $repackageId)->get();
        if (!$repackage->isEmpty()) {
            return Inertia::render('Repackage/EditRepackage', ['repackage' => $repackage[0], 'inventory' => $inventory]);
        }
        return redirect()->route('repackage.list');
    }

    public function updateRepackage(request $request, $repackageId)
    {
        $request->validate([
            'inventory_id' => 'required',
            'quantity' => 'required|numeric',
        ]);
    
        $repackage = ChangeWarehouse::with(['inventory.product'])
            ->where('change_warehouse_id', $repackageId)
            ->first();
    
        $initialQuantity = $repackage->quantity;
        $newQuantity = $request['quantity'];
        $quantityDifference = $newQuantity - $initialQuantity;
    
        $repackage->update([
            'inventory_id' => $request['inventory_id'],
            'quantity' => $newQuantity,
        ]);
    
        $productId = $repackage->inventory->product->product_id;
    
        $warehouse1Inventory = Inventory::where('warehouse_id', 1)
            ->where('product_id', $productId)
            ->first();
    
        if ($warehouse1Inventory) {
            $warehouse1Inventory->quantity -= $quantityDifference;
            $warehouse1Inventory->save();
        }
    
        $warehouse2Inventory = Inventory::where('warehouse_id', 2)
            ->where('product_id', $productId)
            ->first();
    
        if ($warehouse2Inventory) {
            $warehouse2Inventory->quantity += $quantityDifference;
            $warehouse2Inventory->save();
        } else {
            Inventory::create([
                'warehouse_id' => 2,
                'product_id' => $productId,
                'quantity' => $quantityDifference
            ]);
        }
    
        $productsId = ['16', '17'];
        $getProduct = Inventory::with('product')
            ->where('warehouse_id', '1')
            ->whereIn('product_id', $productsId)
            ->get();
    
            return redirect()->route('repackage.list')  ;
    }
   

}