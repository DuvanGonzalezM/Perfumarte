<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchDetail;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SupplyReceptionController extends Controller
{
    public function show(Dispatch $dispatch)
    {
        // Verificar que el despacho esté "en ruta" y pertenezca al punto de venta del usuario
        $dispatch = $dispatch->load([
            'dispatchDetail.inventory.product',
            'dispatchDetail.warehouse.location'
        ]);

        // Verificar si el despacho tiene detalles
        if (!$dispatch->dispatchDetail) {
            return redirect()->back()
                ->with('error', 'El despacho no tiene productos asociados');
        }

        return Inertia::render('Reception/SupplyReception', [
            'dispatch' => $dispatch
        ]);
    }

    public function receive(Request $request, Dispatch $dispatch)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,product_id',
            'products.*.received' => 'required|boolean',
            'products.*.observation' => 'nullable|string',
            'products.*.quantity' => 'required|numeric|min:0'
        ]);

        // DB::transaction(function () use ($dispatch, $validated, $request) {
            // Actualizar estado del despacho
            $dispatch->update(['status' => 'received']);

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
        // });

        return redirect()->route('dispatch.list')
            ->with('success', 'Despacho recibido correctamente');
    }
}