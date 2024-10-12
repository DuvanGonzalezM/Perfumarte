<?php

namespace App\http\Controllers;

use App\Models\Transformation;
use Inertia\Inertia;
use Illuminate\Http\Request;


class LabTransformationController extends Controller
{
    public function getAllTransformation()
    {

        $getLabTransformation = Transformation::with(['inventory.product',])->get();
        
        return Inertia::render('LabTransformations/LabTransformationList', props: ['getLabTransformation' => $getLabTransformation]);
    }




}


