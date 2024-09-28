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
        $suppliesRequest = RequestPrais::with('user.location')->get();
        return Inertia::render('Requests/SuppliesRequestList',props:['suppliesRequest'=>$suppliesRequest]);
    }

    public function detailRequest($requestId){
        $suppliesRequest = RequestPrais::with(['user.location', 'detailRequest.inventory'])-> findOrFail($requestId);
        return Inertia::render('Requests/SuppliesRequestDetails', [
            'requestPrais' => $suppliesRequest,
            'details' => $suppliesRequest->detailRequest,]);

    }

}
