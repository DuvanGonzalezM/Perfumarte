<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Inertia\Inertia;
use Illuminate\Http\Request;



class ProductController extends Controller
{
    public function getAllProducts()
    {
       $getproducts = Product::with('supplier')->where('status', 1)->get();
       $supplierProduct = Supplier::all();
     
       return Inertia::render('Products/ProductsList', [
        'getProducts' => $getproducts,
        'supplierProduct' => $supplierProduct

    ]);
    }

    public function createProduct()
    {
        $supplierProduct = Supplier::all();

        return Inertia::render('Products/CreateProduct', [
            'supplierProduct' => $supplierProduct
        ]);
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'reference' => 'required',
            'measurement_unit' => 'required',
            'commercial_reference' => 'required',
            'category' => 'required',
            'supplier_id' => 'required',
        ]);

        Product::create([
            'reference' => $request['reference'],
            'measurement_unit' => $request['measurement_unit'],
            'commercial_reference' => $request['commercial_reference'],
            'category' => $request['category'],
            'supplier_id' => $request['supplier_id'],
            'status' => 1,

        ]);

        return redirect()->route('products.list', ['message' => '', 'status' => 200]);

    }

    public function editProduct(Request $request, $product_id)
    {
        $request->validate([
            'reference' => 'required|string|max:255',
            'measurement_unit' => 'required|string|max:255',
            'commercial_reference' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'supplier_id' => 'required',
        ]);
        $product = Product::findOrFail($product_id);
        $product->update([
            'reference' => (string) $request->reference,
            'measurement_unit' => (string) $request->measurement_unit,
            'commercial_reference' => (string) $request->commercial_reference,
            'category' => (string) $request->category,
            'supplier_id' => (int) $request->supplier_id,
        ]);

        return redirect()->route('products.list');
    }

    public function disableProduct($product_id)
    {
        $product = Product::findOrFail($product_id);
        $product->update([
            'status' => 0
        ]);
        return redirect()->route('products.list');
    }
}