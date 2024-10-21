<?php

namespace App\http\Controllers;

use App\Models\Inventory;
use App\Models\RequestDetail;
use App\Models\RequestPrais;
use App\Models\Transformation;
use Inertia\Inertia;
use Illuminate\Http\Request;


class LabTransformationController extends Controller
{
    public function getAllTransformation()
    {

        $getLabTransformation = Transformation::with(['inventory.product'])->get();

        return Inertia::render('LabTransformations/LabTransformationList', props: ['getLabTransformation' => $getLabTransformation]);
    }

    public function createLabTransformation()
    {
        $newProduct = Inventory::with('product')->where('warehouse_id', '=', '1')->whereNotIn('product_id', ['16', '17'])->get();
        $requests = RequestPrais::with(['detailRequest.inventory.product', 'user.location'])->where('request_type', 2)->whereIn('status', ['Pendiente', 'En proceso'])->get();
        return Inertia::render('LabTransformations/CreateLabTransformation', ['newProduct' => $newProduct, 'requests' => $requests]);
    }

    public function storeLabTransformation(request $request)
    {
        $warehouse = '2';

        $request->validate([
            'reference' => 'required',
            'escencia' => 'required',
            'dipropileno' => 'required',
            'disolvente' => 'required',
            'request' => 'required',
            'status' => 'required'
        ]);

        $quantityFragance = $request['escencia'] + $request['dipropileno'] + $request['disolvente'];

        $inventory = Inventory::where('warehouse_id', $warehouse)->where('product_id', $request['reference'])->first();

        $escencia = Inventory::where('warehouse_id', '1')->where('product_id', $request['reference'])->first();

        $dipropylene = Inventory::where('warehouse_id', '1')->where('product_id', '16')->first();

        $solvent = Inventory::where('warehouse_id', '1')->where('product_id', '17')->first();


        if ($escencia && $escencia->quantity >= $request['escencia']) {
            $escencia->update([
                'quantity' => $escencia->quantity - $request['escencia'],
            ]);
        } else {
            dd($request['escencia'], $escencia->quantity);
            // return back()->withErrors(['error' => 'No hay suficiente escencia en el almacén 1.']);
        }

        if ($dipropylene && $dipropylene->quantity >= $request['dipropileno']) {
            $dipropylene->update([
                'quantity' => $dipropylene->quantity - $request['dipropileno'],
            ]);
        } else {
            dd($request['dipropileno'], $dipropylene->quantity);
            // return back()->withErrors(['error' => 'No hay suficiente dipropileno en el almacén 1.']);
        }

        if ($solvent && $solvent->quantity >= $request['disolvente']) {
            $solvent->update([
                'quantity' => $solvent->quantity - $request['disolvente'],
            ]);
        } else {
            dd($request['disolvente'], $solvent->quantity);
            // return back()->withErrors(['error' => 'No hay suficiente disolvente en el almacén 1.']);
        }

        if ($inventory) {
            $quantity = $inventory->quantity + $quantityFragance;
            $inventory->update([
                'quantity' => $quantity,
            ]);
        } else {
            $inventory = Inventory::create([
                'warehouse_id' => $warehouse,
                'product_id' => $request['reference'],
                'quantity' => $quantityFragance,
            ]);
        }

        Transformation::create([
            'inventory_id' => $inventory->inventory_id,
            'escence' => $request['escencia'],
            'dipropylene' => $request['dipropileno'],
            'solvent' => $request['disolvente'],
        ]);

        RequestPrais::where('request_id', $request['request'])->update(['status' => $request['status']]);
    }

    public function detailLabTransformation($transformationId)
    {
        $Labtranformation = Transformation::with('inventory.product')->findOrFail($transformationId);

        return Inertia::render('LabTransformations/LabTransformationDetail', [
            'LabtransformationDetail' => $Labtranformation
        ]);
    }
}
