<?php

namespace App\Http\Controllers;
use App\Models\Inventory;
use App\Models\RequestDetail;
use App\Models\RequestPrais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class RequestPraisController extends Controller
{

    public function getAllRequest()
    {
        $user = Auth::user();
        $suppliesRequest = RequestPrais::with('user.location')
            ->where('request_type', '1')
            ->get();

        if ($user->hasRole('Jefe de Operaciones')) {
            $suppliesRequest = RequestPrais::with('user.location')
                ->where('request_type', '1')
                ->where('status', 'Pendiente')
                ->get();
        }
        
        return Inertia::render('Requests/SuppliesRequestList', [
            'suppliesRequest' => $suppliesRequest
        ]);
    }

    public function createRequst(){
        $inventory = Inventory::with('product')->where('warehouse_id', '2')->get();
        return Inertia::render('Requests/NewSuppliesRequest', [
            'inventory' => $inventory,
        ]);
    }

    public function storeRequest(Request $request){
        $user = Auth::user();
        $request->validate([
            'references.*.reference' => 'required|exists:inventories,inventory_id',
            'references.*.quantity' => 'required|integer|min:1'
        ]);
        $requestPrais = RequestPrais::create([
            'request_type' => '1',
            'user_id'=> $user->user_id,
            'status' => 'Por solicitar'
        ]);
        foreach ($request->references as $reference) {
            RequestDetail::create([
                'request_id' => $requestPrais->request_id,
                'inventory_id' => $reference['reference'],
                'quantity' => $reference['quantity']
            ]);
        }
        return redirect()->route('suppliesrequest.list', ['message' => '', 'status' => 200]);
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
    public function showDetail($requestId)
    {
        $requestPrais = RequestPrais::with([
            'detailRequest.inventory.product',
            'user.location'
        ])->findOrFail($requestId);
        $inventory = Inventory::with('product')->where('warehouse_id', '2')->get();
        return Inertia::render('Requests/EditSuppliesRequest', [
            'requestPrais' => $requestPrais,
            'inventory' => $inventory,
        ]);
    }

    public function update(Request $request, $requestId)
    {
        $request->validate([
            'references.*.reference' => 'required|exists:inventories,inventory_id',
            'references.*.quantity' => 'required|integer|min:1'
        ]);
        RequestDetail::where('request_id', $requestId)->delete();

        foreach ($request->references as $reference) {
            RequestDetail::create([
                'request_id' => $requestId,
                'inventory_id' => $reference['reference'],
                'quantity' => $reference['quantity']
            ]);
        }
        $requestPrais = RequestPrais::findOrFail($requestId);
        $requestPrais->status = 'Pendiente';
        $requestPrais->save();

        return redirect()->route('suppliesrequest.list', ['message' => '', 'status' => 200]);

    }

    public function getAllRequestTransformation()
    {
        $transformationRequest = RequestPrais::where('request_type', '2')->get();
        return Inertia::render('RequestTransformation/TransformationRequestList', [
            'transformationRequest' => $transformationRequest
        ]);
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


    public function createTransformation()
    {
        $inventories = Inventory::with('product')->where('warehouse_id', '1')->get();

        return Inertia::render('RequestTransformation/CreateTransformation', [
            'inventories' => $inventories
        ]);
    }

    public function storeTransformation(Request $request)
    {
        $request->validate([
            'references.*.reference' => 'required',
            'references.*.quantity' => 'required',
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
        return redirect()->route('transformationRequest.list', [
            'message' => '',
            'status' => 200
        ]);
    }
}