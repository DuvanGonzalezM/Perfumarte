<?php
namespace App\Http\Controllers;
use App\Models\DamageReturnDetail;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\DamageReturn;
use App\Models\Warehouse;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class DamageReturnController extends Controller
{
    public function getAllDamageReturn()
    {
        $user = auth()->user();

        if ($user->hasRole('Asesor comercial')) {
            $userLocations = $user->location_user()
                ->pluck('location_user.location_id');

            $userWarehouses = Warehouse::whereIn('location_id', $userLocations)
                ->pluck('warehouse_id');

            $damageReturn = DamageReturn::with('damageReturnDetail.warehouse.location')
                ->whereHas('damageReturnDetail', function ($query) use ($userWarehouses) {
                    $query->whereIn('warehouse_id', $userWarehouses);
                })
                ->get();
        } else {
            $damageReturn = DamageReturn::with('damageReturnDetail.warehouse.location')->get();
        }

        return Inertia::render('DamageReturn/DamageReturnList', [
            'damageReturn' => $damageReturn
        ]);
    }

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
            $damageReturn->status = 'Confirmar';
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
                    $damageReturnDetail->received = false;
                    $damageReturnDetail->discarded = false;
                    $damageReturnDetail->save();
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


    public function editDamageReturn($id)
    {
        $user = auth()->user();

        $damageReturn = DamageReturn::with([
            'damagereturndetail.inventory.product',
            'damagereturndetail.warehouse.location'
        ])->findOrFail($id);

        return Inertia::render('DamageReturn/DamageReturnDetail', [
            'damageReturn' => $damageReturn,
        ]);
    }


    public function approvedDamageReturn(Request $request, $id)
    {

        $damageReturn = DamageReturn::findOrFail($id);

        if ($damageReturn->status !== 'Confirmar') {
            return redirect()->back()->withErrors(['error' => 'Esta devolución ya fue procesada y no se puede modificar.']);
        }

        $validated = $request->validate([
            'details' => 'required|array',
            'details.*.damage_return_detail_id' => 'required|exists:damage_return_detail,damage_return_detail_id',
            'details.*.received' => 'nullable|boolean',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.inventory_id' => 'required|exists:inventories,inventory_id',
            'details.*.warehouse_id' => 'required|exists:warehouses,warehouse_id',
            'details.*.observations' => 'required|string',
        ]);

        $damageReturn = DamageReturn::with(['damageReturnDetail.inventory.product'])->findOrFail($id);

        foreach ($validated['details'] as $detailData) {
            $detail = $damageReturn->damageReturnDetail
                ->where('damage_return_detail_id', $detailData['damage_return_detail_id'])
                ->first();

            if (!$detail)
                continue;

            $detail->observations = $detailData['observations'];
            $detail->received = $detailData['received'] ?? false;
            $detail->save();

            if ($detailData['received']) {
                $inventoryOrigin = Inventory::where('inventory_id', $detail->inventory_id)
                    ->where('warehouse_id', $detail->warehouse_id)
                    ->first();

                if (!$inventoryOrigin || $inventoryOrigin->quantity < $detail->damage_quantity) {
                    return redirect()->back()->withErrors(['error' => 'Cantidad insuficiente o inventario no encontrado']);
                }

                $inventoryOrigin->quantity -= $detail->damage_quantity;
                $inventoryOrigin->save();

                $productCategory = $detail->inventory->product->category ?? '';
                $targetWarehouseId = ($productCategory === 'Insumo') ? 3 : 2;

                $inventoryTarget = Inventory::where('product_id', $detail->inventory->product_id)
                    ->where('warehouse_id', $targetWarehouseId)
                    ->first();

                if ($inventoryTarget) {
                    $inventoryTarget->quantity += $detail->damage_quantity;
                    $inventoryTarget->save();
                } else {
                    Inventory::create([
                        'product_id' => $detail->inventory->product_id,
                        'warehouse_id' => $targetWarehouseId,
                        'quantity' => $detail->damage_quantity,
                    ]);
                }
            }
        }

        $damageReturn->status = 'En aprobacion';
        $damageReturn->save();

        return redirect()->route('damageReturn.list')->with('success', 'Devolución aprobada');
    }


    public function approveReturnFinal(Request $request, $id)
    {
        $damageReturn = DamageReturn::with('damageReturnDetail.inventory.product')->findOrFail($id);

        if ($damageReturn->status !== 'En aprobacion') {
            return redirect()->back()->withErrors(['error' => 'Esta devolución no está en estado En aprobación.']);
        }

        foreach ($request->details as $detailData) {
            $detail = $damageReturn->damageReturnDetail
                ->where('damage_return_detail_id', $detailData['damage_return_detail_id'])
                ->first();

            if (!$detail) {
                continue;
            }

            if (!$detail->received && !empty($detailData['discarded'])) {
                continue;
            }

            $detail->discarded = !empty($detailData['discarded']) && $detailData['discarded'] == true;
            $detail->save();

            if ($detail->discarded) {
                $productCategory = $detail->inventory->product->category ?? '';
                $targetWarehouseId = ($productCategory === 'Insumo') ? 3 : 2;

                $inventory = Inventory::where('product_id', $detail->inventory->product_id)
                    ->where('warehouse_id', $targetWarehouseId)
                    ->first();

                if ($inventory && $inventory->quantity >= $detail->damage_quantity) {
                    $inventory->quantity -= $detail->damage_quantity;
                    $inventory->save();
                }
            }
        }

        $damageReturn->status = 'Aprobado';
        $damageReturn->save();

        return redirect()->route('damageReturn.list')->with('success', 'Devolución aprobada y cantidades dadas de baja.');
    }
}