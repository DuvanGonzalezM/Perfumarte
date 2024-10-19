<?php

namespace App\http\Controllers;

use App\Models\Inventory;
use App\Models\Transformation;
use Inertia\Inertia;
use Illuminate\Http\Request;


class LabTransformationController extends Controller
{
    public function getAllTransformation()
    {

        $getLabTransformation = Transformation::with(['inventory.product',])->get();

        return Inertia::render('LabTransformations/LabTransformationList', props: ['getLabTransformation' => $getLabTransformation]);
    }

    public function createLabTransformation()
    {
        $newProduct = Inventory::with('product')->where('warehouse_id', '=', '1')->whereNotIn('product_id',['16','17'])->get();

        return Inertia::render('LabTransformations/CreateLabTransformation', ['newProduct' => $newProduct]);
    }

    public function storeLabTransformation(request $request)
    {

        $warehouse = '2';

        $request->validate([
            'reference' => 'required',
            'escencia' => 'required',
            'dipropileno' => 'required',
            'disolvente' => 'required',
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

        Transformation::create([
            'transformation_id' => $request->transformation_id,
            'inventory_id' => $inventory->inventory_id,
            'quantity' => $request['quantity'],
        ]);

        return redirect()->route('repackage.list', ['message' => '', 'status' => 200]);

    }
}


