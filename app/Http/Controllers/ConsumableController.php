<?php
namespace App\Http\Controllers;
use App\Models\Consumable;
use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class ConsumableController extends Controller
{
    public function getAllConsumable()
    {
        $user = auth()->user();
    
        if ($user->hasRole('Asesor comercial')) {
            $userLocations = $user->location_user()
                ->pluck('location_user.location_id');
    
            $userWarehouses = Warehouse::whereIn('location_id', $userLocations)
                ->pluck('warehouse_id');
    
            $consumable = Consumable::with(['inventory.product', 'warehouse.location', 'user'])
                ->whereHas('warehouse', function ($query) use ($userWarehouses) {
                    $query->whereIn('warehouse_id', $userWarehouses);
                })
                ->get();
        } else {
            $consumable = Consumable::with(['inventory.product', 'warehouse.location', 'user'])->get();
        }

        return Inertia::render('Consumable/ConsumableList', [
            'consumable' => $consumable
        ]);
    }

    public function createConsumable()
    {
        $user = auth()->user();

        if ($user->hasRole(['Gerencia', 'Administrador', 'Jefe de operaciones'])) {
            $warehouseId = 3;
            $warehouse = Warehouse::findOrFail($warehouseId);

            $inventory = Inventory::with('product')
                ->where('warehouse_id', $warehouseId)
                ->get();
        } else {
            $warehouse = $user->location_user[0]->warehouses[0] ?? null;

            if (!$warehouse) {
                return redirect()->back()->withErrors(['error' => 'El usuario no tiene una sede asignada.']);
            }

            $warehouseId = $warehouse->warehouse_id;

            $inventory = Inventory::with('product')
                ->where('warehouse_id', $warehouseId)
                ->whereHas('product', function ($query) {
                    $query->where('category', 'Insumo');
                })
                ->get();
        }

        return Inertia::render('Consumable/ConsumableCreate', [
            'userId' => $user->id,
            'warehouseConsumable' => $warehouse,
            'inventoryConsumable' => $inventory,
        ]);
    }

    public function storeConsumable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consumable.*.references.*.reference' => 'required|exists:inventories,inventory_id',
            'consumable.*.references.*.consumable_quantity' => 'required|integer|min:1',
        ]);

        $errors = [];
        $user = auth()->user();
        $isSpecialRole = $user->hasRole(['Administrador', 'Gerencia', 'Jefe de operaciones']);

        // Validación inicial: verificar cantidades disponibles
        if ($request->has('consumable')) {
            foreach ($request->consumable as $locationIndex => $location) {
                $warehouseId = $location['warehouse_id'] ?? null;

                if ($isSpecialRole) {
                    $warehouseId = 3; // ejemplo: bodega especial
                }

                foreach ($location['references'] as $referenceIndex => $reference) {
                    $inventory = Inventory::where('inventory_id', $reference['reference'] ?? null)
                        ->where('warehouse_id', $warehouseId)
                        ->first();

                    if (!$inventory) {
                        continue;
                    }

                    if ($reference['consumable_quantity'] > $inventory->quantity) {
                        $errors["consumable.{$locationIndex}.references.{$referenceIndex}.consumable_quantity"] =
                            "La cantidad ({$reference['consumable_quantity']}) excede el inventario disponible ({$inventory->quantity}).";
                    }
                }
            }
        }

        // Si falla validación Laravel o validación manual
        if ($validator->fails() || !empty($errors)) {
            return back()
                ->withInput()
                ->withErrors($validator->errors()->merge($errors));
        }

        try {
            $savedConsumables = [];

            foreach ($request->consumable as $location) {
                $warehouseId = $location['warehouse_id'] ?? null;

                if ($isSpecialRole) {
                    $warehouseId = 3;
                }

                foreach ($location['references'] as $reference) {
                    $inventory = Inventory::where('inventory_id', $reference['reference'])
                        ->where('warehouse_id', $warehouseId)
                        ->first();

                    if (!$inventory) {
                        continue;
                    }

                    $consumable = new Consumable();
                    $consumable->inventory_id = $inventory->inventory_id;
                    $consumable->warehouse_id = $warehouseId;
                    $consumable->user_id = $user->user_id;
                    $consumable->consumable_quantity = $reference['consumable_quantity'];
                    $consumable->save();

                    // Actualizar inventario
                    $inventory->quantity -= $reference['consumable_quantity'];
                    $inventory->save();

                    // Cargar relaciones para mostrar en la vista
                    $consumable->load(['inventory.product', 'warehouse.location', 'user']);

                    $savedConsumables[] = $consumable;
                }
            }

            return redirect()->route('consumable.list')
                ->with('success', 'Consumibles registrados exitosamente.')
                ->with('consumables', $savedConsumables);

        } catch (\Exception $e) {
            \Log::error('Error al crear el registro de consumibles: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors([
                    'general' => 'Ocurrió un error inesperado al crear el registro de consumibles. Intenta de nuevo.'
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


    public function approvedConsumableReturn(Request $request, $id)
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