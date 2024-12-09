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
            ->leftJoin('users', 'locations.location_id', '=', 'users.location_id')
            ->leftJoin('model_has_roles', 'users.user_id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereNotIn('locations.name', ['CEDI'])
            ->with(['userLocation' => function($query) {
                $query->whereHas('roles', function($q) {
                    $q->where('name', 'Supervisor');
                })->select('user_id', 'name', 'location_id');
            }])
            ->get();

        $supervisors = User::whereHas('roles', function ($q) {
            $q->where('name', 'Supervisor');
        })->get();

        return Inertia::render('Assignment/AssignmentSupervisor', ['locations' => $locations, 'supervisors' => $supervisors]);

    }

    public function storeAssignment(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,location_id',
            'supervisor_id' => 'required|exists:users,user_id'
        ]);

        $user = User::find($request->supervisor_id);
        $user->update([
            'location_id' => $request->location_id
        ]);

        return redirect()->route('assignment.supervisor');
    }
}