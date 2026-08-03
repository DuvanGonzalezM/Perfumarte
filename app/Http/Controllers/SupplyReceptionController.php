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
        $warehouse = auth()->user()->location_user->first()?->warehouses->first();

        if (! $warehouse) {
            return redirect()->route('dashboard')
                ->with('error', 'Su usuario no tiene una sede con bodega asignada.');
        }

        $despacho = Dispatch::with('dispatchDetail.inventory.product')
            ->where('status', '=', 'En ruta')
            ->whereHas('dispatchDetail', function ($query) use ($warehouse) {
                return $query->where('warehouse_id', $warehouse->warehouse_id);
            })
            ->first();

        return Inertia::render('Reception/SupplyReception', [
            'dispatch' => $despacho,
        ]);
    }

    public function receive(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.dispatchs_detail_id' => 'required|integer|exists:dispatches_detail,dispatchs_detail_id',
            'products.*.received' => 'nullable|boolean',
            'products.*.observation' => 'nullable|string',
            'products.*.quantity' => 'required|numeric|min:0'
        ]);

        if (count($validated['products']) === 0) {
            return redirect()->route('inventory.current', ['message' => '', 'status' => 200]);
        }

        $warehouse = auth()->user()->location_user->first()?->warehouses->first();

        if (! $warehouse) {
            return redirect()->route('dashboard')
                ->with('error', 'Su usuario no tiene una sede con bodega asignada.');
        }

        try {
            return DB::transaction(function () use ($validated, $warehouse) {
                $detailIds = array_column($validated['products'], 'dispatchs_detail_id');

                $details = DispatchDetail::with('inventory')
                    ->whereIn('dispatchs_detail_id', $detailIds)
                    ->where('warehouse_id', $warehouse->warehouse_id)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('dispatchs_detail_id');

                if ($details->count() !== count($detailIds)) {
                    throw new \Exception('El despacho recibido no corresponde a su bodega.');
                }

                if ($details->pluck('dispatch_id')->unique()->count() !== 1) {
                    throw new \Exception('Los detalles recibidos pertenecen a despachos distintos.');
                }

                $dispatch = Dispatch::where('dispatch_id', $details->first()->dispatch_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($dispatch->status !== 'En ruta') {
                    throw new \Exception('Este despacho ya fue recibido.');
                }

                $dispatch->update(['status' => 'Recibido']);

                foreach ($validated['products'] as $product) {
                    $dispatchDetail = $details->get($product['dispatchs_detail_id']);

                if ($dispatchDetail) {
                    $dispatchDetail->update([
                        'received' => $product['received'],
                        'observations' => $product['observation']
                    ]);

                    if ($product['received']) {
                        $inventory = Inventory::where('product_id', $dispatchDetail->inventory->product_id)
                            ->where('warehouse_id', $dispatchDetail->warehouse_id)
                            ->lockForUpdate()
                            ->first();

                        $incomingQuantity = min((float) $product['quantity'], (float) $dispatchDetail->dispatched_quantity);

                        $category = optional($dispatchDetail->inventory->product)->category;

                        $applyMax = ($category !== 'Insumo');
                        $maxQuantity = $applyMax ? 750 : null;

                        if ($inventory) {
                            $currentQuantity = $inventory->quantity;
                            $totalAfterAddition = $currentQuantity + $incomingQuantity;

                            if (!$applyMax || $totalAfterAddition <= $maxQuantity) {
                                $inventory->update([
                                    'quantity' => $totalAfterAddition
                                ]);
                                $dispatchDetail->returned_quantity = 0;
                            } else {
                                $acceptedQuantity = $maxQuantity - $currentQuantity;
                                $returnedQuantity = $incomingQuantity - $acceptedQuantity;

                                $inventory->update([
                                    'quantity' => $maxQuantity
                                ]);
                                $dispatchDetail->returned_quantity = $returnedQuantity;
                            }
                        } else {
                            if (!$applyMax || $incomingQuantity <= $maxQuantity) {
                                $inventory = Inventory::create([
                                    'product_id' => $dispatchDetail->inventory->product_id,
                                    'warehouse_id' => $dispatchDetail->warehouse_id,
                                    'quantity' => $incomingQuantity
                                ]);
                                $dispatchDetail->returned_quantity = 0;
                            } else {
                                $inventory = Inventory::create([
                                    'product_id' => $dispatchDetail->inventory->product_id,
                                    'warehouse_id' => $dispatchDetail->warehouse_id,
                                    'quantity' => $maxQuantity
                                ]);
                                $dispatchDetail->returned_quantity = $incomingQuantity - $maxQuantity;
                            }
                        }
                        $dispatchDetail->save();
                    }
                }
                }

                return redirect()->route('inventory.current', ['message' => '', 'status' => 200]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}