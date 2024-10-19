<?php
namespace App\Http\Controllers;
use App\Models\Dispatch;
use App\Models\DispatchDetail;
use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DispatchController extends Controller
{
    public function getAllDispatch()
    {
        $dispatch = Dispatch::with('dispatchdetail')->get();
        return Inertia::render('Dispatch/DispatchList', props: ['dispatch' => $dispatch]);
    }

    public function detailDispatch($id)
    {
        $detaildispatch = Dispatch::with(['dispatchdetail.inventory.product','dispatchdetail.warehouse.location'])->findOrFail($id);
        return Inertia::render('Dispatch/DispatchDetail', [
            'detaildispatch' => $detaildispatch,
        ]);
    }
    public function createDispatch()
    {
        $warehouses = Warehouse::all();
        $inventory = Inventory::with('product')->where('warehouse_id', 2)->get();
        return Inertia::render('Dispatch/DispatchNew', [
            'warehouses' => $warehouses,
            'inventory' => $inventory,
        ]);

    }
    public function storeDispatch(Request $request)
    {
        // $validatedData = $request->validate([
        //     'products.*.reference' => 'required|exists:inventories,product_id',
        //     'products.*.dispatched_quantity' => 'required|numeric|min:1',
        //     'warehouses' => 'required|string',
        // ]);
        try {
            $dispatch = Dispatch::create([
                'status' => 'Pendiente',
            ]);
            foreach ($request->dispatches as $location) {
                foreach ($location['references'] as $reference) {
                    DispatchDetail::create([
                        'warehouse_id' => $location['warehouse'],
                        'dispatch_id' => $dispatch->dispatch_id,
                        'inventory_id' => $reference['reference'],
                        'dispatched_quantity' => $reference['dispatched_quantity'],
                        'observations' => $reference['observations'],
                    ]);
                }
            }
            return redirect()->route('dispatch.list')->with('success', 'Despacho creado exitosamente.');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}