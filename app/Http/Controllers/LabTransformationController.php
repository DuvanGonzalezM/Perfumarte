<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\RequestPrais;
use App\Models\Transformation;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;


class LabTransformationController extends Controller
{
    public function getAllTransformation()
    {

        $getLabTransformation = Transformation::with(['inventory.product'])->limit(100)->orderBy('transformation_id', 'desc')->get();

        return Inertia::render('LabTransformations/LabTransformationList', props: ['getLabTransformation' => $getLabTransformation]);
    }

    public function createLabTransformation()
    {
        $newProduct = Inventory::with('product')->where('warehouse_id', '=', '1')->whereNotIn('product_id', ['1', '2'])->get();
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

        return DB::transaction(function () use ($request, $warehouse) {
            $quantityFragance = $request['escencia'] + $request['dipropileno'] + $request['disolvente'];

            $inventory = Inventory::where('warehouse_id', $warehouse)->where('product_id', $request['reference'])->first();

            $escencia = Inventory::where('warehouse_id', '1')->where('product_id', $request['reference'])->first();
            $dipropylene = Inventory::where('warehouse_id', '1')->where('product_id', '1')->first();
            $solvent = Inventory::where('warehouse_id', '1')->where('product_id', '2')->first();

            if (!$escencia || $escencia->quantity < $request['escencia']) {
                throw new \Exception('Stock insuficiente de esencia.');
            }
            if (!$dipropylene || $dipropylene->quantity < $request['dipropileno']) {
                throw new \Exception('Stock insuficiente de dipropileno.');
            }
            if (!$solvent || $solvent->quantity < $request['disolvente']) {
                throw new \Exception('Stock insuficiente de disolvente.');
            }

            $escencia->update(['quantity' => $escencia->quantity - $request['escencia']]);
            $dipropylene->update(['quantity' => $dipropylene->quantity - $request['dipropileno']]);
            $solvent->update(['quantity' => $solvent->quantity - $request['disolvente']]);

            if ($inventory) {
                $inventory->update(['quantity' => $inventory->quantity + $quantityFragance]);
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

            return redirect()->route('labTransformation.list')->with('success', 'Transformación registrada correctamente.');
        });
    }

    public function detailLabTransformation($transformationId)
    {
        $Labtranformation = Transformation::with('inventory.product')->findOrFail($transformationId);

        return Inertia::render('LabTransformations/LabTransformationDetail', [
            'LabtransformationDetail' => $Labtranformation
        ]);
    }
    public function editTransformation($transformationId)
    {
        $inventory = Inventory::with('product')->where('warehouse_id', 2)->get();
        $transformation = Transformation::with('inventory.product')->where('transformation_id', $transformationId)->get();
        if (!$transformation->isEmpty()) {
            return Inertia::render('LabTransformations/LabTransformationEdit', ['transformation' => $transformation[0], 'inventory' => $inventory]);
        } else {
            return redirect()->route('labTransformation.list');
        }
    }

    public function updateTransformation(Request $request, $transformationId): RedirectResponse
{
    $request->validate([
        'transformation_id' => 'required',
        'inventory_id' => 'required|exists:inventories,inventory_id',
        'escence' => 'required|numeric|min:0',
        'dipropylene' => 'required|numeric|min:0',
        'solvent' => 'required|numeric|min:0',
    ]);

    // Obtener la transformación con relaciones
    $transformation = Transformation::with(['inventory.product'])->findOrFail($transformationId);

    // Guardar valores iniciales para calcular diferencias
    $initialValues = [
        'escence' => $transformation->escence,
        'dipropylene' => $transformation->dipropylene,
        'solvent' => $transformation->solvent
    ];

    // Actualizar la transformación
    $transformation->update([
        'inventory_id' => $request['inventory_id'],
        'escence' => $request['escence'],
        'dipropylene' => $request['dipropylene'],
        'solvent' => $request['solvent'],
    ]);

    
    $escenciaProductId = $transformation->inventory->product->product_id; 
    $escenciaInventory = Inventory::firstOrCreate(
        ['warehouse_id' => 1, 'product_id' => $escenciaProductId],
        ['quantity' => 0]
    );
    $escenciaInventory->quantity -= ($request['escence'] - $initialValues['escence']);
    $escenciaInventory->save();

    $dipropilenoInventory = Inventory::firstOrCreate(
        ['warehouse_id' => 1, 'product_id' => 1],
        ['quantity' => 0]
    );
    $dipropilenoInventory->quantity -= ($request['dipropylene'] - $initialValues['dipropylene']);
    $dipropilenoInventory->save();

    $disolventeInventory = Inventory::firstOrCreate(
        ['warehouse_id' => 1, 'product_id' => 2],
        ['quantity' => 0]
    );
    $disolventeInventory->quantity -= ($request['solvent'] - $initialValues['solvent']);
    $disolventeInventory->save();

    $totalChange = ($request['escence'] + $request['dipropylene'] + $request['solvent']) - 
                  ($initialValues['escence'] + $initialValues['dipropylene'] + $initialValues['solvent']);

    $productoTerminadoInventory = Inventory::firstOrCreate(
        [
            'warehouse_id' => 2,
            'product_id' => $transformation->inventory->product->product_id
        ],
        ['quantity' => 0]
    );
    $productoTerminadoInventory->quantity += $totalChange;
    $productoTerminadoInventory->save();

    return redirect()->route('LabTransformation.list');
}
}
