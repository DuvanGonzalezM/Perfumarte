<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssignmentController extends Controller
{
    public function getAllSupervisor()
    {
        $locations = Location::select('locations.location_id', 'locations.name')
            ->whereNotIn('locations.location_id', ['1'])
            ->with([
                'userLocation' => function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->where('name', 'Supervisor');
                    })->select('user_id', 'name', 'location_id');
                }
            ])->get();

        $supervisors = User::select('user_id', 'name')->whereHas('roles', function ($rolSupervisor) {
            $rolSupervisor->where('name', 'Supervisor');
        })->get();

      

        return Inertia::render('Assignment/AssignmentSupervisor', ['locations' => $locations, 'supervisors' => $supervisors]);

    }

    public function updateAssignment(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'user_id' => 'required',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update([
            'location_id' => $request->location_id,
        ]);

        return back();
    }
}