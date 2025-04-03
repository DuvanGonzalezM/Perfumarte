<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Inertia\Inertia;
use Illuminate\Http\Request;



class SupplierController extends Controller
{
    public function getAllSuppliers()
    {
       $getsuppliers = Supplier::all()->where('status', 1);
       
       return Inertia::render('Supplier/supplierList', ['getSuppliers' => $getsuppliers,]);
    
    }


    public function createSupplier()
    {
        $newSupplier = Supplier::all();

        return Inertia::render('Supplier/supplierCreate', [
            'newSupplier' => $newSupplier
        ]);
    }

    public function storeSupplier(Request $request)
    {

        $request->validate([
            'nit' => 'required',
            'name' => 'required',
            'country' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        Supplier::create([
            'nit' => $request['nit'],
            'name' => $request['name'],
            'country' => $request['country'],
            'address' => $request['address'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'status' => 1,

        ]);

        return redirect()->route('suppliers.list', ['message' => '', 'status' => 200]);

    }

    public function editSupplier(Request $request, $supplier_id)
    {
        $request->validate([
            'nit' => 'required',
            'name' => 'required',
            'country' => 'required',
            'address' => 'required',    
            'phone' => 'required',
            'email' => 'required',
        ]);
        $supplier = Supplier::findOrFail($supplier_id);
        $supplier->update([
            'nit' => (string) $request->nit,
            'name' => (string) $request->name,
            'country' => (string) $request->country,
            'address' => (string) $request->address,
            'phone' => (string) $request->phone,
            'email' => (string) $request->email,
        ]);

        return redirect()->route('suppliers.list');
    }

    public function disableSupplier($supplier_id)
    {
        $supplier = Supplier::findOrFail($supplier_id);
        $supplier->update([
            'status' => 0
        ]);
        return redirect()->route('suppliers.list');
    }
}