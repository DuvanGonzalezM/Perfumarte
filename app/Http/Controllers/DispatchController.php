<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\DispatchDetail;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DispatchController extends Controller
{
    public function getAllDispatch()
    {
        $dispatch = Dispatch::with('dispatchdetail')->get();
        return Inertia::render('Dispatch/DispatchList', props: ['dispatch' => $dispatch]);
    }

    public function detailDispatch($id)
    {
        $dispatch = DispatchDetail::with(['inventory.product'])->findOrFail($id);
        // dd($dispatch);
        return Inertia::render('Dispatch/DispatchDetail', [
            'dispatch' => $dispatch,
        ]);
    }
}
