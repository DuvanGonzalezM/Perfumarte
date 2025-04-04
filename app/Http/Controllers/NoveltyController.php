<?php

namespace App\Http\Controllers;

use App\Models\Novelty;
use App\Models\Warehouse;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;



class NoveltyController extends Controller
{
    public function getAllNovelties()
    {
       $getnovelties = Novelty::with('warehouse')->get();

       $user = auth()->user();
       $userName = User::where('user_id', $user->user_id)->value('name');
     
       return Inertia::render('Novelty/noveltiesList', ['getNovelties' => $getnovelties, 'userName' => $userName]);
    
    }


    public function createNovelty()
    {
        $newNovelty = Novelty::with('warehouse')->get();


        return Inertia::render('Novelty/createNovelty', [
            'newNovelty' => $newNovelty,
          
        ]);
    }   

    public function storeNovelty(Request $request)
    {

        $request->validate([
           'type_novelty',
           'description_novelty',
           'warehouse_id'
        ]);

        Novelty::create([
            'type_novelty' => $request['type_novelty'],
            'description_novelty' => $request['description_novelty'],
            'warehouse_id' => $request['warehouse_id'],
        ]);

        return redirect()->route('novelty.list', ['message' => '', 'status' => 200]);

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