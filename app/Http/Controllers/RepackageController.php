<?php

namespace App\Http\Controllers;

use App\Models\ChangeWarehouse;
use App\Models\Inventory;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $warehouse = '2';
        $esenceWarehouse = '1';
    
        $request->validate([
            'reference' => 'required',
            'quantity' => 'required|numeric|min:0',
        ]);
    
        try {
            /*
             * El movimiento entre bodegas es un traslado: descuento en origen,
             * ingreso en destino y registro del cambio. Se escribía sin
             * transacción, de modo que un fallo a mitad podía descontar el
             * stock de esencias sin acreditarlo en la bodega de reenvase.
             *
             * La comprobación de stock vivía además fuera de toda transacción y
             * sin bloqueo, con la lectura desacoplada de la escritura.
             */
            return DB::transaction(function () use ($request, $warehouse, $esenceWarehouse) {
                $inventoryOut = Inventory::where('warehouse_id', $esenceWarehouse)
                    ->where('product_id', $request['reference'])
                    ->lockForUpdate()
                    ->first();

                if (! $inventoryOut) {
                    throw new \Exception('El producto no existe en la bodega de origen.');
                }

                if ($inventoryOut->quantity < $request['quantity']) {
                    throw new \Exception('No hay suficiente stock en la bodega de origen. Disponible: '.$inventoryOut->quantity);
                }

                $inventory = Inventory::firstOrNew([
                    'warehouse_id' => $warehouse,
                    'product_id' => $request['reference']
                ]);

                $inventory->quantity = $inventory->quantity + $request['quantity'];
                $inventory->save();

                $inventoryOut->quantity -= $request['quantity'];
                $inventoryOut->save();

                ChangeWarehouse::create([
                    'inventory_id' => $inventory->inventory_id,
                    'quantity' => $request['quantity'],
                ]);

                return redirect()->route('repackage.list')->with('success', 'Reenvase realizado correctamente');
            });
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
    
        // Mismo traslado entre bodegas que storeRepackage, con el mismo defecto:
        // tres escrituras encadenadas sin transacción ni bloqueo.
        return DB::transaction(function () use ($request, $repackageId) {
            $repackage = ChangeWarehouse::with(['inventory.product'])
                ->where('change_warehouse_id', $repackageId)
                ->lockForUpdate()
                ->firstOrFail();

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
                ->lockForUpdate()
                ->first();

            if ($warehouse1Inventory) {
                $warehouse1Inventory->quantity -= $quantityDifference;
                $warehouse1Inventory->save();
            }

            $warehouse2Inventory = Inventory::where('warehouse_id', 2)
                ->where('product_id', $productId)
                ->lockForUpdate()
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

            return redirect()->route('repackage.list');
        });
    }
}