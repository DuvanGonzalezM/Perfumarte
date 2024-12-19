<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchDetail;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupplyReceptionController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $dispatchDetails = DispatchDetail::with('dispatch', 'inventory.product')->where("warehouse_id", $user->location->warehouses[0]->warehouse_id)
            ->whereHas('dispatch', function ($query) {
            return $query->where('status', '=', 'En ruta');
        })->get();

        return Inertia::render('Reception/SupplyReception', [
            'dispatchDetails' => $dispatchDetails
        ]);
    }

    public function receive(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.received' => 'nullable|boolean',
            'products.*.observation' => 'nullable|string',
            'products.*.quantity' => 'required|numeric|min:0'
        ]);
        // DB::transaction(function () use ($dispatch, $validated, $request) {
            // Actualizar estado del despacho
        if(count($request['products']) > 0){
            $dispatch = Dispatch::findOrFail($request['products'][0]['dispatch_id']);
            $dispatch->update(['status' => 'Recibido']);

            // Actualizar cada detalle del despacho

            foreach ($request['products'] as $product) {
                $dispatchDetail = DispatchDetail::where('dispatchs_detail_id', $product['dispatchs_detail_id'])
                    ->first();

                if ($dispatchDetail) {
                    $dispatchDetail->update([
                        'received' => $product['received'],
                        'observations' => $product['observation']
                    ]);

                    // Actualizar inventario del punto de venta si fue recibido
                    // if ($product['received']) {
                    //     $inventory = Inventory::where('inventory_id', $dispatchDetail->inventory_id)->first();
                    //     $inventory->update([
                    //         'quantity' => ($inventory->quantity + $product['quantity'])
                    //     ]);

                    //     $pointOfSaleWarehouse = auth()->user()->location->warehouse;

                    //     $pointOfSaleWarehouse->inventory()->updateOrCreate(
                    //         ['product_id' => $product['product_id']],
                    //         ['quantity' => DB::raw('quantity + ' . $product['quantity'])]
                    //     );
                    // }
                }
            }
        }
        // });
        return redirect()->route('dispatch.show', ['message' => '', 'status' => 200]);
    }
}