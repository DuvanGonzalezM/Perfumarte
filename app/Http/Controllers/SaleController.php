<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(){
        $sales = Sale::with('user')->where('created_at', '>=', date('Y-m-d'))->get();
        return Inertia::render('Sale/SalesList', ['sales' => $sales]);
    }
}
