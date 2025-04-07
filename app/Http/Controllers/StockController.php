<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;

class StockController extends Controller
{
    public function getStocks()
    {
        $warehouses = Warehouse::whereHas('location.userLocation', function ($query) {
            return $query->where('user_id', '=', session('user_id'));
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
        return $query->where('user_id', '=', session('user_id'));
    })->get();

    $inventory = Inventory::with('product.supplier')
        ->where('warehouse_id', $warehouses->first()->warehouse_id)
        ->get();

    $locations = Location::with('warehouses')
        ->whereDoesntHave('userLocation', function ($query) {
            return $query->where('user_id', session('user_id'));
        })
        ->get();

    // Obtener todos los productos disponibles
    $products = Product::with('supplier')->get();

    return Inertia::render('Stock/InventoryMultiple', [
        'locations' => $locations,
        'warehouses' => $warehouses,
        'inventory' => $inventory,
        'products' => $products
    ]);
}

public function updateInventory(Request $request)
{
    $validated = $request->validate([
        'warehouse_id' => 'required',
        'products' => 'required|array',
        'products.*.product_id' => 'required',
        'products.*.quantity' => 'required|numeric',
    ]);

    // Obtener la bodega
    $warehouse = Warehouse::findOrFail($validated['warehouse_id']);

    // Obtener el inventario actual de la bodega
    $currentInventory = Inventory::where('warehouse_id', $warehouse->warehouse_id)->get();

    // Convertir el inventario actual en un array para facilitar la actualización
    $currentInventoryMap = $currentInventory->keyBy('product_id');

    // Identificar los productos a eliminar
    $productsToDelete = $currentInventory->filter(function($item) use ($validated) {
        return !in_array($item->product_id, array_column($validated['products'], 'product_id'));
    });

    // Eliminar los productos que ya no están en el inventario
    foreach ($productsToDelete as $product) {
        $product->delete();
    }

    // Actualizar cantidades de productos existentes y agregar nuevos productos
    foreach ($validated['products'] as $product) {
        // Si el producto ya existe en el inventario, actualizamos la cantidad
        if (isset($currentInventoryMap[$product['product_id']])) {
            $inventoryItem = $currentInventoryMap[$product['product_id']];
            $inventoryItem->update(['quantity' => $product['quantity']]);
        } else {
            // Si el producto no existe, lo agregamos al inventario
            Inventory::create([
                'warehouse_id' => $warehouse->warehouse_id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity']
            ]);
        }
    }

    // Obtener el inventario actualizado
    $inventory = Inventory::with('product.supplier')
        ->where('warehouse_id', $warehouse->warehouse_id)
        ->get();

    return redirect()->route('stock.multiple');
}

}
