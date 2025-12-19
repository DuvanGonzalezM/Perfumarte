<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Obtiene todos los supervisores y sus ubicaciones asignadas
     * - Los administradores ven todos los supervisores de todas las sedes
     * - Los subdirectores ven solo los supervisores de su zona
     * - Otros roles ven todos los supervisores excepto los de la ubicación 1
     *
     * @return \Inertia\Response
     */
    public function getAllSupervisor()
    {
        $user = Auth::user();
        
        // Consulta base para ubicaciones con sus supervisores
        $locationsQuery = Location::query();
        
        // Filtros según el rol del usuario
        if ($user->hasRole('Administrador')) {
            // Administradores ven todas las ubicaciones excepto la 1
            $locationsQuery->whereNotIn('locations.location_id', ['1']);
        } elseif ($user->hasRole('Subdirector')) {
            // Subdirectores ven solo su zona
            $locationsQuery->where('locations.zone_id', '=', $user->zone_id);
        } else {
            // Otros roles ven todas excepto ubicación 1
            $locationsQuery->whereNotIn('locations.location_id', ['1']);
        }
        
        // Cargar la relación con los supervisores
        $locations = $locationsQuery->with([
            'users_location' => function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'Supervisor');
                });
            }
        ])->get();

        // Obtener supervisores disponibles para asignar
        $supervisorsQuery = User::role('Supervisor');
        
        // Si no es administrador, solo puede ver los supervisores que le pertenecen
        if (!$user->hasRole('Administrador')) {
            $supervisorsQuery->where('boss_user', $user->user_id);
        }
        
        $supervisors = $supervisorsQuery->select('user_id', 'name')->get();

        return Inertia::render('Assignment/AssignmentSupervisor', [
            'locations' => $locations,
            'supervisors' => $supervisors,
            'isAdmin' => $user->hasRole('Administrador')
        ]);
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

        if ($location->users_location->count() > 0) {
            foreach ($location->users_location as $user) {
                $location->users_location()->detach($user->user_id);
            }
        }

        if ($request->user_id !== null) {
            User::findOrFail($request->user_id)->location_user()->attach($request->location_id);
        }

        return back();
    }


    /* Funciones para la asignacion de asesores */


    public function getAllLocation()
    {
        $user = Auth::user();
        $getSede = Location::select('locations.location_id', 'locations.name')->whereHas('users_location', function ($query) use ($user) {
            $query->where('location_user.user_id', '=', $user->user_id);
        })
            ->whereNotIn('locations.location_id', ['1'])->get();

        $advisors = User::select('user_id', 'name')->whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Asesor comercial');
        })->get();

        return Inertia::render('Assignment/ListLocation', ['getSede' => $getSede, 'advisors' => $advisors]);
    }

    public function getAllAdvisor($location_id)
    {
        $user = Auth::user();
        $getSede = Location::where('location_id', $location_id)->whereHas('users_location', function ($query) use ($user) {
            $query->where('location_user.user_id', '=', $user->user_id);
        })
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
        })->where('boss_user', '=', $user->user_id)
            ->whereDoesntHave('location_user', function ($query) use ($location_id) {
                $query->where('location_user.location_id', '!=', $location_id);
            })
            ->get();

        $users = User::whereHas('roles', function ($roladvisor) {
            $roladvisor->where('name', 'Usuario');
        })->where('boss_user', '=', $user->user_id)
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

        User::findOrFail($request['caja1']['user_id'])->update(['enabled' => 1, 'location_id' => $request['location_id']]);
        $location->users_location()->attach($request['caja1']['user_id']);

        if ($request['caja2']['user_id'] !== '' && $request['caja2']['user_id'] !== null) {
            User::findOrFail($request['caja2']['user_id'])->update(['enabled' => 1, 'location_id' => $request['location_id']]);
            $location->users_location()->attach($request['caja2']['user_id']);
        }

        if (count($request['advisorList']) > 0) {
            foreach ($request['advisorList'] as $advisor) {
                $location->users_location()->attach($advisor['user_id']);
            }
        }

        return redirect()->route('list.location')->with('success', 'Asignación exitosa.');
    }

}
