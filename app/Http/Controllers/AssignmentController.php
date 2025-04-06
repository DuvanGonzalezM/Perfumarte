<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function getAllSupervisor()
    {
        $locations = Location::whereNotIn('locations.location_id', ['1'])
            ->with([
                'users_location' => function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->where('name', 'Supervisor');
                    });
                }
            ])->get();

        $supervisors = User::select('user_id', 'name')->whereHas('roles', function ($query) {
            $query->where('name', 'Supervisor');
        })->get();

        return Inertia::render('Assignment/AssignmentSupervisor', ['locations' => $locations, 'supervisors' => $supervisors]);
    }

    public function updateAssignment(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'user_id' => 'nullable',
        ]);

        $location = Location::where('location_id', $request->location_id)
            ->with([
                'users_location' => function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->where('name', 'Supervisor');
                    });
                }
            ])
            ->first();
            
        if($location->users_location->count() > 0){
            foreach ($location->users_location as $user) {
                $location->users_location()->detach($user->user_id);
            }
        }

        if($request->user_id !== null){
            User::findOrFail($request->user_id)->location_user()->attach($request->location_id);
        }

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
        $getSede = Location::where('location_id', $location_id)
            ->with([
                'users_location' => function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['Usuario', 'Asesor comercial']);
                    });
                }
            ])
            ->first();
        $advisors = User::whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Asesor comercial');
            })
            ->whereDoesntHave('location_user', function ($query) use ($location_id) {
                $query->where('location_user.location_id', '!=', $location_id);
            })
            ->get();

        $users = User::whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Usuario');
            })
            ->whereDoesntHave('location_user', function ($query) use ($location_id) {
                $query->where('location_user.location_id', '!=', $location_id);
            })
            ->get();

        return Inertia::render('Assignment/DetailAssignAdvisor', ['getSede' => $getSede, 'advisors' => $advisors, 'users' => $users]);
    }

    public function StoreAdvisor(Request $request)
    {
        $request->validate([
            'caja1.user_id' => 'required',
        ]);

        $location = Location::where('location_id', $request['location_id'])
        ->with([
            'users_location' => function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Usuario', 'Asesor comercial']);
                });
            }
        ])
        ->first();

        foreach ($location->users_location as $userAssigned) {
            User::findOrFail($userAssigned['user_id'])->update(['enabled' => 0]);
            $location->users_location()->detach($userAssigned['user_id']);
        }
        
        User::findOrFail($request['caja1']['user_id'])->update(['enabled' => 1]);
        $location->users_location()->attach($request['caja1']['user_id']);

        if ($request['caja2']['user_id'] !== '' && $request['caja2']['user_id'] !== null) {
            User::findOrFail($request['caja2']['user_id'])->update(['enabled' => 1]);
            $location->users_location()->attach($request['caja2']['user_id']);
        }

        if (count($request['advisorList']) > 0) {
            foreach ($request['advisorList'] as $advisor) {
                $location->users_location()->attach($advisor['user_id']);
            }
        }

        return redirect()->route('list.location')->with('success', 'Despacho creado exitosamente.');
    }

}
