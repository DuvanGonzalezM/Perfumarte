<?php

namespace App\Http\Controllers;

use App\Models\ProductEntry;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function getAllOrders()
    {
        $purchaseOrders = PurchaseOrder::with('productEntryOrder.product.supplier')->get();
        return Inertia::render('ChiefOperating/OrdersList', props: ['purchaseOrders' => $purchaseOrders]);
    }

    public function createOrder()
    {
        $suppliers = Supplier::with('products')->get();
        return Inertia::render('ChiefOperating/CreateOrder', ['suppliers' => $suppliers]);
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'supplier' => 'required',
            'supplier_order' => 'required',
            'references' => 'required|array',
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'supplier_order' => $request->supplier_order
        ]);

        foreach ($request->references as $reference) {
            ProductEntry::create([
                'purchase_order_id' => $purchaseOrder->purchase_order_id,
                'product_id' => $reference['reference'],
                'quantity' => $reference['quantity']
            ]);
        }

        return redirect()->route('orders.list');
    }

    public function editOrders($orderId)
    {
        $purchaseOrder = PurchaseOrder::with('productEntryOrder.product.supplier.products')->where('purchase_order_id', $orderId)->get();
        if (!$purchaseOrder->isEmpty()) {
            return Inertia::render('ChiefOperating/EditOrders', ['purchaseOrder' => $purchaseOrder[0]]);
        } else {
            return redirect()->route('orders.list');
        }
    }

    public function updateOrders(Request $request, ProductEntry $productEntry)
    {
        $request->validate(['quantity' => 'required']);
        $productEntry->update($request->input());
        return redirect('productEntryOrder');
    }
}
