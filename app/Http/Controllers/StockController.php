<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Product;

class StockController extends Controller
{
    public function getStocks()
    {
        $warehouses = Warehouse::whereHas('location.userLocation', function ($query) {
            return $query->where('user_id', '=', auth()->id());
        })->get();
        return Inertia::render('Stock/Dashboard', ['warehouses' => $warehouses]);
    }

    public function getInventory($warehouse_id)
{
    $warehouse = Warehouse::findOrFail($warehouse_id);
    $inventory = Inventory::with('product.supplier')
        ->where('warehouse_id', $warehouse_id)
        ->get();
    return [
        'warehouse' => $warehouse,
        'inventory' => $inventory
    ];
}

    public function getAllInventory($warehouse_id)
    {
        $inventory = $this->getInventory($warehouse_id);
        return Inertia::render('Stock/Inventory', ['inventory' => $inventory['inventory'], 'warehouse' => $inventory['warehouse']]);
    }

    public function getMultipleInventory()
{
    $warehouses = Warehouse::whereHas('location.userLocation', function ($query) {
        return $query->where('user_id', '=', auth()->id());
    })->get();

    // Sin bodegas asignadas, ->first()->warehouse_id era un 500.
    $inventory = $warehouses->isEmpty()
        ? collect()
        : Inventory::with('product.supplier')
            ->where('warehouse_id', $warehouses->first()->warehouse_id)
            ->get();

    $locations = Location::with('warehouses')
        ->whereDoesntHave('userLocation', function ($query) {
            return $query->where('user_id', auth()->id());
        })
        ->get();

    $products = Product::with('supplier')->get();

    return Inertia::render('Stock/InventoryMultiple', [
        'locations' => $locations,
        'warehouses' => $warehouses,
        'inventory' => $inventory,
        'products' => $products
    ]);
}

    /**
     * Ajuste manual de inventario de una bodega.
     *
     * Cuatro defectos acumulados antes de esta corrección:
     *
     *  1. La ruta vivía bajo `can:Ver Stock`, un permiso de LECTURA, de modo
     *     que un asesor comercial podía ejecutar una operación destructiva.
     *  2. `warehouse_id` no se validaba ni se comprobaba su pertenencia:
     *     cualquiera podía apuntar a la bodega de otra sede.
     *  3. Las filas ausentes del payload se BORRABAN físicamente y arrastraban
     *     por cascada `sale_details`, destruyendo el histórico de ventas. Con
     *     `products: []` se vaciaba el inventario de una sede entera.
     *  4. Todo ello sin transacción.
     */
    public function updateInventory(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|integer|exists:warehouses,warehouse_id',
            'products' => 'present|array',
            'products.*.product_id' => 'required|integer|exists:products,product_id',
            'products.*.quantity' => 'required|numeric|min:0',
        ]);

        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);

        if (! $this->userCanManageWarehouse($warehouse)) {
            abort(403, 'La bodega seleccionada no pertenece a su sede.');
        }

        DB::transaction(function () use ($validated, $warehouse) {
            $currentInventory = Inventory::where('warehouse_id', $warehouse->warehouse_id)
                ->lockForUpdate()
                ->get();

            $currentInventoryMap = $currentInventory->keyBy('product_id');
            $submittedIds = array_column($validated['products'], 'product_id');

            /*
             * Las referencias que ya no vienen en el payload se ponen a cero en
             * lugar de borrarse: la fila de inventario es el destino de las FK
             * de sale_details, dispatches_detail, audit_inventory y otras seis
             * tablas, todas con ON DELETE CASCADE y ninguna con SoftDeletes.
             */
            foreach ($currentInventory as $item) {
                if (! in_array($item->product_id, $submittedIds)) {
                    $item->update(['quantity' => 0]);
                }
            }

            foreach ($validated['products'] as $product) {
                if (isset($currentInventoryMap[$product['product_id']])) {
                    $currentInventoryMap[$product['product_id']]->update([
                        'quantity' => $product['quantity'],
                    ]);
                } else {
                    Inventory::create([
                        'warehouse_id' => $warehouse->warehouse_id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                    ]);
                }
            }
        });

        return redirect()->route('stock.multiple');
    }

    /**
     * Una bodega es administrable si pertenece a alguna sede del usuario.
     *
     * Los perfiles centrales (Administrador, Gerencia, Jefe de operaciones) no
     * tienen filas en location_user y operan sobre toda la red; para ellos el
     * control efectivo es el permiso `Editar Stock` que exige la ruta.
     */
    private function userCanManageWarehouse(Warehouse $warehouse): bool
    {
        $locationIds = auth()->user()->location_user->pluck('location_id');

        if ($locationIds->isEmpty()) {
            return true;
        }

        return $locationIds->contains($warehouse->location_id);
    }
}
