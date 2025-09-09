<?php
namespace App\Http\Controllers;
use App\Models\DamageReturnDetail;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\DamageReturn;
use Inertia\Inertia;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Validator;

class DamageReturnController extends Controller
{
    public function getAllDamageReturn()
    {
        $damageReturn = DamageReturn::with('damageReturnDetail.warehouse.location')->get();
        return Inertia::render('DamageReturn/DamageReturnList', props: ['damageReturn' => $damageReturn]);
    }


    public function editDamageReturn($id)
    {
        $user = auth()->user();
        $damageReturn = DamageReturn::with(['damagereturndetail.inventory.product', 'damagereturndetail.warehouse.location'])->findOrFail($id);

        if ($damageReturn->status == 'En ') {

            if (!$user->hasRole('Jefe de Operaciones')) {
                return Inertia::render('DamageReturn/DamageReturnDetail', [
                    'damageReturn' => $damageReturn,
                ]);
            }

            $warehouses = Warehouse::with('location')->whereDoesntHave('location.userLocation', function ($query) {
                return $query->where('user_id', '=', session('user_id'));
            })->get();
            $requests = RequestPrais::with(['detailRequest.inventory.product', 'user.location'])->where('request_type', 1)->where('status', 'Pendiente')->get();
            $inventory = Inventory::with('product')->whereIn('warehouse_id', [2, 3])->get();
            return Inertia::render('DamageReturn/DamageReturnEdit', [
                'inventory' => $inventory,
                'damageReturn' => $damageReturn,
                'warehouses' => $warehouses,
                'requests' => $requests
            ]);

        } else {
            return Inertia::render('DamageReturn/DamageReturnDetail', [
                'damageReturn' => $damageReturn,
            ]);
        }
    }

    // public function updateDispatch(Request $request, $dispatchId)
    // {
    //     $request->validate([
    //         'dispatches.*.warehouse' => 'required',
    //         'dispatches.*.references.*.reference' => 'required',
    //         'dispatches.*.references.*.dispatched_quantity' => 'required',
    //     ]);
    //     try {

    //         DispatchDetail::where('dispatch_id', $dispatchId)->delete();

    //         foreach ($request->dispatches as $location) {
    //             foreach ($location['references'] as $reference) {

    //                 $inventory = Inventory::with('product')
    //                     ->where('inventory_id', $reference['reference'])
    //                     ->whereIn('warehouse_id', [2, 3])
    //                     ->firstOrFail();

    //                 if ($inventory->quantity < $reference['dispatched_quantity']) {
    //                     throw new \Exception("No hay suficiente stock para {$inventory->product->reference}. Disponible: {$inventory->quantity}, Solicitado: {$reference['dispatched_quantity']}");
    //                 }

    //                 DispatchDetail::create([
    //                     'dispatch_id' => $dispatchId,
    //                     'warehouse_id' => $location['warehouse'],
    //                     'inventory_id' => $reference['reference'],
    //                     'dispatched_quantity' => $reference['dispatched_quantity'],
    //                     'received' => 0
    //                 ]);
    //             }
    //         }

    //         return redirect()->route('dispatch.list')->with('success', 'Despacho actualizado correctamente');


    //     } catch (\Exception $e) {

    //         return redirect()->back()
    //             ->withInput()
    //             ->withErrors(['error' => $e->getMessage()]);
    //     }
    // }

    // public function approvedDispatch($id)
    // {
    //     $dispatch = Dispatch::with('dispatchdetail')->findOrFail($id);

    //     foreach ($dispatch->dispatchdetail as $detail) {

    //         $inventoryOrigin = Inventory::where('inventory_id', $detail->inventory_id)
    //             ->whereIn('warehouse_id', [2, 3])
    //             ->first();

    //         if (!$inventoryOrigin) {
    //             return redirect()->back()->withErrors(['error' => 'Inventario no encontrado en bodega principal']);
    //         }

    //         if ($inventoryOrigin->quantity < $detail->dispatched_quantity) {
    //             return redirect()->back()->withErrors(['error' => 'Cantidad insuficiente en bodega principal']);
    //         }

    //         $inventoryOrigin->quantity -= $detail->dispatched_quantity;
    //         $inventoryOrigin->save();
    //     }

    //     $dispatch->status = 'En ruta';
    //     $dispatch->save();

    //     return redirect()->route('dispatch.list')->with('success', 'Despacho aprobado');
    // }

    public function createDamageReturn()
    {
        $user = auth()->user();

        $warehouse = $user->location_user[0]->warehouses[0];

        $inventory = Inventory::with('product')
            ->where('warehouse_id', $warehouse->warehouse_id)
            ->get();

        return Inertia::render('DamageReturn/DamageReturnCreate', [
            'userId' => $user->id,        
            'warehouseReturn' => $warehouse,         
            'inventoryReturn' => $inventory,               
        ]);
    }
    public function storeDamageReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'damageReturn.*.warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'damageReturn.*.references.*.reference' => 'required|exists:inventories,inventory_id',
            'damageReturn.*.references.*.damage_quantity' => 'required|integer|min:1',
            'damageReturn.*.references.*.observations' => 'required|string',
        ]);

        $errors = [];

        if ($request->has('damageReturn')) {
            foreach ($request->damageReturn as $locationIndex => $location) {
                $warehouseId = $location['warehouse_id'] ?? null;

                foreach ($location['references'] as $referenceIndex => $reference) {
                    $inventory = Inventory::where('inventory_id', $reference['reference'] ?? null)
                        ->where('warehouse_id', $warehouseId)
                        ->first();

                    if ($inventory && $reference['damage_quantity'] > $inventory->quantity) {
                        $errors["damageReturn.{$locationIndex}.references.{$referenceIndex}.damage_quantity"] =
                            "La cantidad ({$reference['damage_quantity']}) excede el inventario disponible ({$inventory->quantity}).";
                    }
                }
            }
        }

        if ($validator->fails() || !empty($errors)) {
            return back()
                ->withInput()
                ->withErrors($validator->errors()->merge($errors));
        }

        try {

            $damageReturn = new DamageReturn();
            $damageReturn->status = 'En revision';
            $damageReturn->save();

            foreach ($request->damageReturn as $location) {
                $warehouseId = $location['warehouse_id'];

                foreach ($location['references'] as $reference) {
                    $inventory = Inventory::where('inventory_id', $reference['reference'])
                        ->where('warehouse_id', $warehouseId)
                        ->firstOrFail();

                    $damageReturnDetail = new DamageReturnDetail();
                    $damageReturnDetail->damage_return_id = $damageReturn->damage_return_id;
                    $damageReturnDetail->inventory_id = $inventory->inventory_id;
                    $damageReturnDetail->warehouse_id = $warehouseId;
                    $damageReturnDetail->damage_quantity = $reference['damage_quantity'];
                    $damageReturnDetail->observations = $reference['observations'];
                    $damageReturnDetail->save();

                    $inventory->quantity -= $reference['damage_quantity'];
                    $inventory->save();
                }
            }

            return redirect()->route('damageReturn.list')
                ->with('success', 'Devolución creada correctamente.');

        } catch (\Exception $e) {
            \Log::error('Error al crear la devolución: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors([
                    'general' => 'Ocurrió un error inesperado al crear la devolución. Intenta de nuevo.'
                ]);
        }
    }
    // public function getReturnedDispatch($id)
    // {
    //     $user = auth()->user();

    //     $dispatch = Dispatch::with([
    //         'dispatchdetail.inventory.product',
    //         'dispatchdetail.warehouse.location'
    //     ])->findOrFail($id);

    //     if ($dispatch->status == 'Recibido' && $user->hasRole('Monitoreo')) {
    //         return Inertia::render('Dispatch/DispatchDetail', [
    //             'dispatch' => $dispatch
    //         ]);
    //     }

    //     return Inertia::render('Dispatch/Dispatchdetail', [
    //         'dispatch' => $dispatch
    //     ]);
    // }

    // public function storeReturnedQuantities(Request $request)
    // {
    //     $request->validate([
    //         'dispatch_id' => 'required|exists:dispatches,dispatch_id',
    //         'details' => 'required|array',
    //         'details.*.id' => 'required|exists:dispatches_detail,dispatchs_detail_id',
    //         'details.*.returned_quantity' => 'required|numeric',
    //     ]);

    //     foreach ($request->details as $detailData) {
    //         $detail = DispatchDetail::with('inventory')->find($detailData['id']);

    //         $detail->returned_quantity = $detailData['returned_quantity'];
    //         $detail->save();

    //         $inventory = Inventory::where('product_id', $detail->inventory->product_id)
    //             ->where('warehouse_id', 2)
    //             ->first();

    //         if ($inventory) {
    //             $inventory->quantity += $detailData['returned_quantity'];
    //             $inventory->save();
    //         } else {
    //             Inventory::create([
    //                 'product_id' => $detail->inventory->product_id,
    //                 'warehouse_id' => 2,
    //                 'quantity' => $detailData['returned_quantity'],
    //             ]);
    //         }
    //     }

    //     $dispatch = Dispatch::findOrFail($request->dispatch_id);
    //     $dispatch->status = 'Devuelto';
    //     $dispatch->save();


    //     return redirect()->route('dispatch.list')->with('success', 'Cantidades devueltas registradas en inventario.');
    // }
}