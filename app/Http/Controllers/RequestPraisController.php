<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\RequestDetail;
use App\Models\RequestPrais;
use Inertia\Inertia;

class RequestPraisController extends Controller
{
    public function getAllRequest()
    {
        $suppliesRequest = RequestPrais::with('user.location')->where('request_type', '=', '1')->get();
        return Inertia::render('Requests/SuppliesRequestList', props: ['suppliesRequest' => $suppliesRequest]);
    }
    public function detailRequest($requestId)
    {
        $suppliesRequest = RequestPrais::with([
            'user.location',
            'detailRequest.inventory.product',
        ])->findOrFail($requestId);
        return Inertia::render('Requests/SuppliesRequestDetails', [
            'requestPrais' => $suppliesRequest
        ]);
    }

    public function getAllRequestTransformation()
    {
        $transformationRequest = RequestPrais::where('request_type', '=', '2')->get();
        return Inertia::render('RequestTransformation/TransformationRequestList', props: ['transformationRequest' => $transformationRequest]);
    }

    public function createTransformation()
    {

        $inventories = Inventory::with('product')->where('warehouse_id', '=', '1')->get();

        return Inertia::render('RequestTransformation/CreateTransformation', ['inventories' => $inventories]);
    }

    public function storeTransformation(Request $request)
    {
        $request->validate([
            'references' => 'required|array',
        ]);

        $transformationRequest = RequestPrais::create([
            'request_type' => '2',
            'user_id' => $request->user()->user_id,
            'status' => 'Pendiente'
        ]);

        foreach ($request->references as $reference) {

            RequestDetail::create([
                'request_id' => $transformationRequest->request_id,
                'inventory_id' => $reference['reference'],
                'quantity' => $reference['quantity']
            ]);
        }
        return redirect()->route('transformationRequest.list', ['message' => '', 'status' => 200]);
    }



    public function detailTransformation($requestId)
    {
        $tranformationRequest = RequestPrais::with([
            'user.location',
            'detailRequest.inventory.product',
        ])->findOrFail($requestId);

        return Inertia::render('RequestTransformation/TransformationDetail', [
            'transformationRequest' => $tranformationRequest
        ]);
    }
}