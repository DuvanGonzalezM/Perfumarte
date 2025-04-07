<?php

namespace App\Http\Controllers;

use App\Events\CreatePurchaseOrder;
use App\Models\Inventory;
use App\Models\ProductEntry;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\RequestPrais;
use App\Models\RequestDetail;
use App\Models\User;
use App\Notifications\PurchaseOrderCreate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Pest\Plugins\Parallel\Handlers\Laravel;

class PurchaseOrderController extends Controller
{
    public function getAllOrders()
    {
        $purchaseOrders = PurchaseOrder::with('productEntryOrder.product.supplier')->get();
        return Inertia::render('PurchaseOrder/OrdersList', ['purchaseOrders' => $purchaseOrders]);
    }

    public function createOrder()
    {
        $suppliers = Supplier::with('products')->get();
        return Inertia::render('PurchaseOrder/CreateOrder', ['suppliers' => $suppliers]);
    }

    public function storeOrder(Request $request)
    {
        try {
            $request->validate([
                'supplier' => 'required',
                'supplier_order' => 'required',
                'references.*.reference' => 'required',
                'references.*.quantity' => 'required',
            ]);
            $purchaseOrder = PurchaseOrder::create([
                'supplier_order' => $request->supplier_order
            ]);
    
            foreach ($request->references as $reference) {
                $warehouse = 3;
                if (strtoupper($reference['unity']) == 'KG') {
                    $warehouse = 1;
                    $reference['quantity'] *= 1000;
                }
    
                $inventory = Inventory::where('warehouse_id', $warehouse)->where('product_id', $reference['reference'])->first();
                if ($inventory) {
                    $quantity = $inventory->quantity + $reference['quantity'];
                    $inventory->update([
                        'quantity' => $quantity
                    ]);
                } else{
                    Inventory::create([
                        'warehouse_id' => $warehouse,
                        'product_id' => $reference['reference'],
                        'quantity' => $reference['quantity']
                    ]);
                }

                ProductEntry::create([
                    'purchase_order_id' => $purchaseOrder->purchase_order_id,
                    'product_id' => $reference['reference'],
                    'quantity' => $reference['quantity'],
                    'batch' => $reference['batch']
                ]);
            }
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'Administrador');
            })->get();
            foreach ($users as $user) {
                $user->notify(new PurchaseOrderCreate($purchaseOrder));
            }
            broadcast(new CreatePurchaseOrder(PurchaseOrder::with('productEntryOrder.product.supplier')->findOrFail($purchaseOrder->purchase_order_id)));
            return redirect()->route('orders.list', ['message' => '', 'status' => 200]);
        } catch (\ErrorException $e) {
            dd($e);
        }
    }

    public function detailOrder($orderId)
    {
        $purchaseOrder = PurchaseOrder::with('productEntryOrder.product.supplier.products')->findOrFail($orderId);
        if ($purchaseOrder) {
            return Inertia::render('PurchaseOrder/OrderDetail', ['purchaseOrder' => $purchaseOrder]);
        } else {
            return redirect()->route('orders.list');
        }
    }
    public function editOrders($orderId)
    {
        $purchaseOrder = PurchaseOrder::with('productEntryOrder.product.supplier.products')->where('purchase_order_id', $orderId)->get();
        if (!$purchaseOrder->isEmpty()) {
            return Inertia::render('PurchaseOrder/EditOrders', ['purchaseOrder' => $purchaseOrder[0]]);
        } else {
            return redirect()->route('orders.list');
        }
    }

    public function updateOrders(Request $request, $purchaseOrderId): RedirectResponse
    {
        $request->validate([
            'supplier_order' => 'required',
            'references' => 'required|array',
        ]);
    
        PurchaseOrder::where('purchase_order_id', $purchaseOrderId)->update([
            'supplier_order' => $request->supplier_order
        ]);
    
        foreach ($request->references as $reference) {

            $warehouse = (strtoupper($reference['unity'] ?? '') == 'KG') ? 1 : 3;
            $quantity = ($warehouse == 1) ? ($reference['quantity'] * 1000) : $reference['quantity'];
    
            if (isset($reference['product_entry_id'])) {

                $oldEntry = ProductEntry::find($reference['product_entry_id']);
                $quantityDifference = $quantity - $oldEntry->quantity;

                ProductEntry::where('product_entry_id', $reference['product_entry_id'])->update([
                    'product_id' => $reference['reference'],
                    'quantity' => $quantity,
                    'batch' => $reference['batch'] ?? $oldEntry->batch
                ]);
    
                $inventory = Inventory::where('warehouse_id', $warehouse)
                                    ->where('product_id', $reference['reference'])
                                    ->first();
    
                if ($inventory) {
                    $inventory->quantity += $quantityDifference;
                    $inventory->save();
                } else {

                    Inventory::create([
                        'warehouse_id' => $warehouse,
                        'product_id' => $reference['reference'],
                        'quantity' => $quantity
                    ]);
                }
            } else {

                ProductEntry::create([
                    'purchase_order_id' => $purchaseOrderId,
                    'product_id' => $reference['reference'],
                    'quantity' => $quantity,
                    'batch' => $reference['batch'] ?? null
                ]);
    
                $inventory = Inventory::where('warehouse_id', $warehouse)
                                    ->where('product_id', $reference['reference'])
                                    ->first();
    
                if ($inventory) {
                    $inventory->quantity += $quantity;
                    $inventory->save();
                } else {
                    Inventory::create([
                        'warehouse_id' => $warehouse,
                        'product_id' => $reference['reference'],
                        'quantity' => $quantity
                    ]);
                }
            }
        }
    
        return redirect()->route('orders.list')->with('success', 'Orden actualizada correctamente');
    }
}
