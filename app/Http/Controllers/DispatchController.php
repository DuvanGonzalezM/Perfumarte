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
        $dispatch = Dispatch::with('dispatchdetail')->get();
        return Inertia::render('Dispatch/DispatchList', props: ['dispatch' => $dispatch]);
    }

    public function detailDispatch($id)
    {
        $detaildispatch = DispatchDetail::with(['dispatch', 'inventory.product', 'inventory.warehouse.location'])->findOrFail($id);
        return Inertia::render('Dispatch/DispatchDetail', [
            'detaildispatch' => $detaildispatch,
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