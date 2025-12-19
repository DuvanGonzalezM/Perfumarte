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

        $repackageList = ChangeWarehouse::with(['inventory.product',])->limit(100)->orderBy('change_warehouse_id', 'desc')->get();
        return Inertia::render('Repackage/RepackageList', props: ['repackageList' => $repackageList]);
    }

    public function createRepackage()
    {

        $productsId = ['1', '2'];

        $getProduct = Inventory::with('product')->where('warehouse_id', '1')->whereIn('product_id', $productsId)->get();

        return Inertia::render('Repackage/CreateRepackage', ['getProduct' => $getProduct]);
    }
    public function storeRepackage(Request $request)
    {
        $warehouse = '2'; // Bodega destino
        $esenceWarehouse = '1'; // Bodega origen
    
        $request->validate([
            'reference' => 'required',
            'quantity' => 'required|numeric|min:0',
        ]);
    
        // Obtener inventario en bodega origen (warehouse_1)
        $inventoryOut = Inventory::where('warehouse_id', $esenceWarehouse)
                                ->where('product_id', $request['reference'])
                                ->first();
    
    
        // Validar que haya suficiente cantidad
        if ($inventoryOut->quantity < $request['quantity']) {
            return back()->withErrors([
                'quantity' => 'No hay suficiente stock en la bodega de origen. Disponible: '.$inventoryOut->quantity
            ]);
        }
    
        // Procesar el reenvase (sin transacciones)
        try {
            // Actualizar o crear en bodega destino (warehouse_2)
            $inventory = Inventory::firstOrNew([
                'warehouse_id' => $warehouse,
                'product_id' => $request['reference']
            ]);
    
            $inventory->quantity = $inventory->quantity + $request['quantity'];
            $inventory->save();
    
            // Actualizar bodega origen (warehouse_1)
            $inventoryOut->quantity -= $request['quantity'];
            $inventoryOut->save();
    
            // Registrar el movimiento
            ChangeWarehouse::create([
                'inventory_id' => $inventory->inventory_id,
                'quantity' => $request['quantity'],
            ]);
    
            return redirect()->route('repackage.list')->with('success', 'Reenvase realizado correctamente');
    
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Ocurrió un error al procesar el reenvase: '.$e->getMessage()
            ]);
        }
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
    
        $productsId = ['1', '2'];
        $getProduct = Inventory::with('product')
            ->where('warehouse_id', '1')
            ->whereIn('product_id', $productsId)
            ->get();
    
            return redirect()->route('repackage.list')  ;
    }
   

}