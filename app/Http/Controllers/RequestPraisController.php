<?php

namespace App\Http\Controllers;

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
            'requestPrais' => $suppliesRequest,
            'details' => $suppliesRequest->detailRequest
        ]);
    }

    public function getAllRequestTransformation()
    {
        $transformationRequest = RequestPrais::where('request_type', '=', '2')->get();
        return Inertia::render('RequestTransformation/TransformationRequestList', props: ['transformationRequest' => $transformationRequest]);
    }
}
