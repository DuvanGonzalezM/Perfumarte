<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function getAllOrders(){
        $orders = PurchaseOrder::get();
        $products = Product::with('supplier')->get();
        $supplier = Supplier::with('product')->get();

        dd($supplier, $products);
    }
}
