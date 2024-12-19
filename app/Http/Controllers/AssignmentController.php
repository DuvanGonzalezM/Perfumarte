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

        User::where('location_id', $request->location_id)->update([
            'location_id' => null,
        ]);
        $user = User::findOrFail($request->user_id);
        $user->update([
            'location_id' => $request->location_id,
        ]);

        return back();
    }


    /* Funciones para la asignacion de asesores */


    public function getAllLocation()
    {
        $getSede = Location::select('locations.location_id', 'locations.name')
            ->whereNotIn('locations.location_id', ['1'])->get();

        $advisors = User::select('user_id', 'name')->whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Asesor comercial');
        })->get();

        return Inertia::render('Assignment/ListLocation', ['getSede' => $getSede, 'advisors' => $advisors]);
    }

    public function getAllAdvisor($location_id)
    {
        $getSede = Location::findOrFail($location_id);

        $advisors = User::select('user_id', 'name')->whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Asesor comercial');
        })->get();

        return Inertia::render('Assignment/DetailAssignAdvisor', ['getSede' => $getSede, 'advisors' => $advisors]);
    }

    public function StoreAdvisor(Request $request)
    {

        $location = Location::findOrFail($request['caja1']['location_id']);
        User::findOrFail($request['caja1']['user_id'])->location_user()->detach();
        $location->users_location()->attach($request['caja1']['user_id']);
        
        if ($request['caja2']['user_id'] !== '') {
            User::findOrFail($request['caja2']['user_id'])->location_user()->detach();
            $location->users_location()->attach($request['caja2']['user_id']);
        }
        if (count($request['advisorList']) > 0) {
            foreach ($request['advisorList'] as $advisor) {
                User::findOrFail($advisor['user_id'])->location_user()->detach();
                $location->users_location()->attach($advisor['user_id']);
            }
        }
        return redirect()->route('list.location')->with('success', 'Despacho creado exitosamente.');
    }

}
