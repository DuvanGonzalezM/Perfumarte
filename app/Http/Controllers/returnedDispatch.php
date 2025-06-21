<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Dispatch;
use App\Models\Warehouse;
use App\Models\Inventory;
use Inertia\Inertia;
use App\Models\DispatchDetail;

class returnedDispatch extends Controller
{

    public function getReturnedDispatch($id)
    {
        $user = auth()->user();

        $dispatch = Dispatch::with([
            'dispatchdetail.inventory.product',
            'dispatchdetail.warehouse.location'
        ])->findOrFail($id);

        if ($dispatch->status == 'Recibido' && $user->hasRole('Monitoreo')) {
            return Inertia::render('Dispatch/DispatchDetail', [
                'dispatch' => $dispatch
            ]);
        }

        // Para todos los demás casos, solo mostrar el detalle
        return Inertia::render('Dispatch/Dispatchdetail', [
            'dispatch' => $dispatch
        ]);
    }

    public function storeReturnedQuantities(Request $request)
    {
        $request->validate([
            'dispatch_id' => 'required|exists:dispatches,dispatch_id',
            'details' => 'required|array',
            'details.*.id' => 'required|exists:dispatches_detail,dispatchs_detail_id',
            'details.*.returned_quantity' => 'required|numeric',
        ]);

        foreach ($request->details as $detailData) {
            $detail = DispatchDetail::with('inventory')->find($detailData['id']);

            // Actualizar la cantidad devuelta
            $detail->returned_quantity = $detailData['returned_quantity'];
            $detail->save();

            // Sumar al inventario de bodega 2
            $inventory = Inventory::where('product_id', $detail->inventory->product_id)
                ->where('warehouse_id', 2)
                ->first();

            if ($inventory) {
                $inventory->quantity += $detailData['returned_quantity'];
                $inventory->save();
            } else {
                Inventory::create([
                    'product_id' => $detail->inventory->product_id,
                    'warehouse_id' => 2,
                    'quantity' => $detailData['returned_quantity'],
                ]);
            }
        }

        $dispatch = Dispatch::findOrFail($request->dispatch_id);
        $dispatch->status = 'Devuelto';
        $dispatch->save();


        return redirect()->route('dispatch.list')->with('success', 'Cantidades devueltas registradas en inventario.');
    }
}