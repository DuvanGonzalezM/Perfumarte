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
        $dispatch = Dispatch::with('dispatchdetail.warehouse.location')->limit(100)->orderBy('dispatch_id', 'desc')->get();
        return Inertia::render('Dispatch/DispatchList', props: ['dispatch' => $dispatch]);
    }


    public function editDispatch($id)
    {
        $user = auth()->user();
        $dispatch = Dispatch::with(['dispatchdetail.inventory.product', 'dispatchdetail.warehouse.location'])->findOrFail($id);

        if ($dispatch->status == 'En aprobacion') {

            if (!$user->hasRole('Jefe de Operaciones')) {
                return Inertia::render('Dispatch/DispatchDetail', [
                    'dispatch' => $dispatch,
                ]);
            }

            $warehouses = Warehouse::with('location')->whereDoesntHave('location.userLocation', function ($query) {
                return $query->where('user_id', '=', session('user_id'));
            })->get();
            $requests = RequestPrais::with(['detailRequest.inventory.product', 'user.location'])->where('request_type', 1)->where('status', 'Pendiente')->get();
            $inventory = Inventory::with('product')->whereIn('warehouse_id', [2, 3])->get();
            return Inertia::render('Dispatch/DispatchEdit', [
                'inventory' => $inventory,
                'dispatch' => $dispatch,
                'warehouses' => $warehouses,
                'requests' => $requests
            ]);

        } else {
            return Inertia::render('Dispatch/DispatchDetail', [
                'dispatch' => $dispatch,
            ]);
        }
    }

    public function updateDispatch(Request $request, $dispatchId)
    {
        $request->validate([
            'dispatches.*.warehouse' => 'required',
            'dispatches.*.references.*.reference' => 'required',
            'dispatches.*.references.*.dispatched_quantity' => 'required',
        ]);
        try {

            DispatchDetail::where('dispatch_id', $dispatchId)->delete();

            foreach ($request->dispatches as $location) {
                foreach ($location['references'] as $reference) {

                    $inventory = Inventory::with('product')
                        ->where('inventory_id', $reference['reference'])
                        ->whereIn('warehouse_id', [2, 3])
                        ->firstOrFail();

                    if ($inventory->quantity < $reference['dispatched_quantity']) {
                        throw new \Exception("No hay suficiente stock para {$inventory->product->reference}. Disponible: {$inventory->quantity}, Solicitado: {$reference['dispatched_quantity']}");
                    }

                    DispatchDetail::create([
                        'dispatch_id' => $dispatchId,
                        'warehouse_id' => $location['warehouse'],
                        'inventory_id' => $reference['reference'],
                        'request_id' => $location['request_id'],
                        'dispatched_quantity' => $reference['dispatched_quantity'],
                        'received' => 0
                    ]);
                }
            }

            return redirect()->route('dispatch.list')->with('success', 'Despacho actualizado correctamente');


        } catch (\Exception $e) {

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function approvedDispatch($id)
    {
        $dispatch = Dispatch::with('dispatchdetail')->findOrFail($id);
        $requestList = [];
        foreach ($dispatch->dispatchdetail as $detail) {

            $inventoryOrigin = Inventory::where('inventory_id', $detail->inventory_id)
                ->whereIn('warehouse_id', [2, 3])
                ->first();

            if (!$inventoryOrigin) {
                return redirect()->back()->withErrors(['error' => 'Inventario no encontrado en bodega principal']);
            }

            if ($inventoryOrigin->quantity < $detail->dispatched_quantity) {
                return redirect()->back()->withErrors(['error' => 'Cantidad insuficiente en bodega principal']);
            }

            $inventoryOrigin->quantity -= $detail->dispatched_quantity;
            $inventoryOrigin->save();

            if ($detail->request_id) {
                if (!in_array($detail->request_id, $requestList)) {
                    $requestList[] = $detail->request_id;
                }
            }
        }

        $dispatch->status = 'En ruta';
        $dispatch->save();

        $requests = RequestPrais::whereIn('request_id', $requestList)->where('status', 'Pendiente')->get();
        foreach ($requests as $request) {
            $request->status = 'Despachado';
            $request->save();
        }

        return redirect()->route('dispatch.list')->with('success', 'Despacho aprobado');
    }


    public function createDispatch()
    {
        $warehouses = Warehouse::with('location')->whereDoesntHave('location.userLocation', function ($query) {
            return $query->where('user_id', '=', session('user_id'));
        })->get();
        $requests = RequestPrais::with(['detailRequest.inventory.product', 'user','location'])->where('request_type', 1)->where('status', 'Pendiente')->get();
        $inventory = Inventory::with('product')
            ->whereIn('warehouse_id', [2, 3])
            ->get();
        return Inertia::render('Dispatch/DispatchNew', [
            'inventory' => $inventory,
            'warehouses' => $warehouses,
            'requests' => $requests
        ]);

    }
    public function storeDispatch(Request $request)
    {
        $request->validate([
            'dispatches.*.warehouse' => 'required|exists:warehouses,warehouse_id',
            'dispatches.*.references.*.reference' => 'required|exists:inventories,inventory_id',
            'dispatches.*.references.*.dispatched_quantity' => 'required|integer|min:1',
        ]);

        try {

            $dispatch = new Dispatch();
            $dispatch->status = 'En aprobacion';
            $dispatch->save();

            foreach ($request->dispatches as $location) {
                foreach ($location['references'] as $reference) {
                    $inventory = Inventory::where('inventory_id', $reference['reference'])
                        ->whereIn('warehouse_id', [2, 3])
                        ->firstOrFail();

                    $detail = new DispatchDetail();
                    $detail->warehouse_id = $location['warehouse'];
                    $detail->dispatch_id = $dispatch->dispatch_id;
                    $detail->inventory_id = $inventory->inventory_id;
                    $detail->request_id = $location['request_id'];
                    $detail->dispatched_quantity = $reference['dispatched_quantity'];
                    $detail->received = 0;
                    $detail->save();

                }
            }

            return redirect()->route('dispatch.list')->with('success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el despacho: ' . $e->getMessage()]);
        }
    }

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

            $detail->returned_quantity = $detailData['returned_quantity'];
            $detail->save();

            $inventory = Inventory::where('product_id', $detail->inventory->product_id)
                ->whereIn('warehouse_id', [2, 3])
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