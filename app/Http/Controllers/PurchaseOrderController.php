<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use View;

class PurchaseOrderController extends Controller
{
    public function getAllOrders()
    {
        $orders = PurchaseOrder::get();
        $products = Product::with('supplier')->get();
        $supplier = Supplier::with('product')->get();

        dd($supplier, $products);
    }

    public function edit(PurchaseOrder $purchase_order): View
    {
        return view('EditPurchaseOrder')->with('purchase_order', $purchase_order);
    }

    public function update(Request $request, $purchase_order_id)
    {
        $request->validate([
            'supplier_order' => 'required|string|max:255',
        ]);
        $purchase_order = PurchaseOrder::findOrFail($purchase_order_id);
        $purchase_order->supplier_order = $request->supplier_order;
        $purchase_order->save();
        return redirect()->route('purchase_orders.edit', $purchase_order->$purchase_order_id)
            ->with('success', 'Orden de compra actualizada exitosamente.');
    }
}
