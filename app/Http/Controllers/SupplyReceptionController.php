<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchDetail;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupplyReceptionController extends Controller
{
    public function show()
    {
        $dispatchDetails = DispatchDetail::with('dispatch', 'inventory.product')->where("warehouse_id", auth()->user()->location_user[0]->warehouses[0]->warehouse_id)
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

        if(count($request['products']) > 0){
            $dispatch = Dispatch::findOrFail($request['products'][0]['dispatch_id']);
            
            DB::transaction(function () use ($dispatch, $validated, $request) {
             
                $dispatch->update(['status' => 'Recibido']);

            
                foreach ($request['products'] as $product) {
                    $dispatchDetail = DispatchDetail::where('dispatchs_detail_id', $product['dispatchs_detail_id'])
                        ->first();

                    if ($dispatchDetail) {
                        $dispatchDetail->update([
                            'received' => $product['received'],
                            'observations' => $product['observation']
                        ]);

                    
                        if ($product['received']) {
                           
                            $inventory = Inventory::where('product_id', $dispatchDetail->inventory->product_id)
                                ->where('warehouse_id', $dispatchDetail->warehouse_id)
                                ->first();

                            if($inventory){
                                $inventory->update([
                                    'quantity' => ($inventory->quantity + $product['quantity'])
                                ]);
                            }else{
                             
                                $inventory = Inventory::create([
                                    'product_id' => $dispatchDetail->inventory->product_id,
                                    'warehouse_id' => $dispatchDetail->warehouse_id,
                                    'quantity' => $product['quantity']
                                ]);
                            }
                        }
                    }
                }
            });
        }
        return redirect()->route('dispatch.show', ['message' => '', 'status' => 200]);
    }
}