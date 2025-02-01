<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function sales(){
        $sales = Sale::with('user')->where('created_at', '>=', date('Y-m-d'))->get();
        return Inertia::render('Sale/SalesList', ['sales' => $sales]);
    }

    public function createSales(){
        $user = Auth::user();
        $location_id = $user->location_user[0]->location_id;
        $assessors = User::whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Asesor comercial');
            })
            ->whereDoesntHave('location_user', function ($query) use ($location_id) {
                $query->where('location_user.location_id', '!=', $location_id);
            })
            ->get();
        $warehouses = $user->location_user[0]->warehouses;
        $inventory = null;
        if(count($warehouses) > 0){
            $inventory = Inventory::with(relations: 'product')->where('warehouse_id', $warehouses[0]->warehouse_id)->get();
        }
        
        return Inertia::render('Sale/CreateSale', [ 'assessors' => $assessors, 'inventory' => $inventory ]);
    }
}
