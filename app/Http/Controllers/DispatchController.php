<?php
namespace App\Http\Controllers;
use App\Models\Dispatch;
use App\Models\DispatchDetail;
use App\Models\Inventory;
use App\Models\RequestPrais;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DispatchController extends Controller
{
    public function getAllDispatch()
    {
        $dispatch = Dispatch::with('dispatchdetail.warehouse.location')->get();
        return Inertia::render('Dispatch/DispatchList', props: ['dispatch' => $dispatch]);
    }

    public function detailDispatch($id)
    {
        $dispatch = Dispatch::with(['dispatchdetail.inventory.product', 'dispatchdetail.warehouse.location'])->findOrFail($id);
        return Inertia::render('Dispatch/DispatchDetail', [
            'dispatch' => $dispatch,
        ]);
    }
    public function createDispatch()
    {
        $warehouses = Warehouse::with('location')->whereDoesntHave('location.userLocation', function ($query) {
            return $query->where('user_id', '=', session('user_id'));
        })->get();
        $requests = RequestPrais::with(['detailRequest.inventory.product', 'user.location'])->where('request_type', 1)->where('status', 'Pendiente')->get();
        $inventory = Inventory::with('product')->where('warehouse_id', 2)->get();
        return Inertia::render('Dispatch/DispatchNew', [
            'inventory' => $inventory,
            'warehouses' => $warehouses,
            'requests' => $requests
        ]);

    }
    public function storeDispatch(Request $request)
    {
        $request->validate([
            'dispatches.*.warehouse' => 'required',
            'dispatches.*.references.*.reference' => 'required',
            'dispatches.*.references.*.dispatched_quantity' => 'required',
        ]);
        try {
            $dispatch = Dispatch::create([
                'status' => 'En ruta',
            ]);
            foreach ($request->dispatches as $location) {
                foreach ($location['references'] as $reference) {
                    $inventoryWarehouse2 = Inventory::where('inventory_id', $reference['reference'])
                        ->with('product')
                        ->where('warehouse_id', 2)
                        ->first();
                    if ($inventoryWarehouse2) {
                        if ($inventoryWarehouse2->quantity >= $reference['dispatched_quantity']) {
                            $inventoryWarehouse2->quantity -= $reference['dispatched_quantity'];
                            $inventoryWarehouse2->save();
                            DispatchDetail::create([
                                'warehouse_id' => $location['warehouse'],
                                'dispatch_id' => $dispatch->dispatch_id,
                                'inventory_id' => $inventoryWarehouse2->inventory_id,
                                'dispatched_quantity' => $reference['dispatched_quantity']
                            ]);
                            $productId = $inventoryWarehouse2->product_id;
                            $inventoryDestinationLocation = Inventory::where('warehouse_id', $location['warehouse'])
                                ->where('product_id', $productId)
                                ->first();

                            if ($inventoryDestinationLocation) {
                                $inventoryDestinationLocation->quantity += $reference['dispatched_quantity'];
                                $inventoryDestinationLocation->save();
                            } else {
                                Inventory::create([
                                    'warehouse_id' => $location['warehouse'],
                                    'product_id' => $productId, 
                                    'quantity' => $reference['dispatched_quantity'],
                                ]);
                            }
                        } else {
                            return redirect()->back()->withErrors(['error' => 'Cantidad insuficiente en la bodega de despacho para la referencia: ' . $inventoryWarehouse2->product->reference]);
                        }
                    } else {
                        return redirect()->back()->withErrors(['error' => 'Inventario no encontrado en la bodega de despacho para la referencia: ' . $inventoryWarehouse2->product->reference]);
                    }
                }
            }
            return redirect()->route('dispatch.list')->with('success', 'Despacho creado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al crear el despacho: ' . $e->getMessage()]);
        }
    }
}