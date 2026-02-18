<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::with('roles');
        if(!Auth::user()->hasRole('TI')){
            $users->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'TI');
            });
        }
        $users = $users->get();
        $roles = Role::all();
        $zones = Zone::all();
        $boss = User::with(['roles', 'zone'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Subdirector')->orWhere('name', 'Supervisor');
            })
            ->get(['user_id', 'name', 'zone_id']);
        return Inertia::render('Users/UsersList', ['users' => $users, 'roles' => $roles, 'zones' => $zones, 'boss' => $boss]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:' . User::class,
        ]);


        $user = User::create([
            'username' => (string) $request->username,
            'name' => (string) $request->name,
            'password' => Hash::make(Str::password(16, true, true, true, false)),
            'boss_user' => (int) $request->boss_user,
            'enabled' => (bool) $request->enabled,
            'location_id' => (int) $request->location_id,
            'default_password' => true,
            'zone_id' => (int) $request->zone_id,
        ]);

        $user->syncRoles($request->role_id);
        

        event(new Registered($user));
        return redirect()->route('users.list');
    }

    public function editUser(Request $request, $user_id)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user_id . ',user_id',
            'name' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($user_id);
        $user->update([
            'username' => (string) $request->username,
            'name' => (string) $request->name,
            'boss_user' => (int) $request->boss_user ?? null,
            'enabled' => (bool) $request->enabled ?? false,
            'zone_id' => (int) $request->zone_id ?? null,
            'default_password' => $request->has('default_password') ? 0 : $user->default_password,
        ]);

        return redirect()->route('users.list');
    }

    public function destroyUser($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            $user->delete();
            return redirect()->route('users.list');
           
        } catch (\Exception $e) {
            return back();
        }
    }

    public function detailUser($user_id)
    {
        $user = User::with('roles', 'permissions')->findOrFail($user_id);
        $currentUser = Auth::user();
        $zones = Zone::all();
        $roles = Role::with('permissions');
        if(!$currentUser->hasRole('TI')){
            $roles = $roles->where('name', '!=', 'TI');
            if($user->hasRole('TI')){
                return redirect()->route('users.list');
            }
        }
        $roles = $roles->get();
        $permissions = Permission::all();
        $boss = User::select('user_id', 'name')->whereHas('roles', function ($query) {
            $query->where('name', 'Subdirector')->orWhere('name', 'Supervisor');
        })->get();
        if ($user) {
            return Inertia::render('Users/UserDetail', ['user' => $user, 'roles' => $roles, 'zones' => $zones, 'permissions' => $permissions, 'boss' => $boss]);
        } else {
            return redirect()->route('users.list');
        }
    }

    public function updateUserRolePermission(Request $request, $user_id)
    {
        $request->validate([
            'roles' => 'required',
                'username' => 'required|string|max:255|unique:users,username,' . $user_id . ',user_id',
                'name' => 'required|string|max:255',
                // 'boss_user' => 'required|integer',
                // 'enabled' => 'required|boolean',
    
            ]);
    
        $user = User::findOrFail($user_id);
        $user->update([
            'username' => (string) $request->username,
            'name' => (string) $request->name,
            'boss_user' => (int) $request->boss_user ?? null,
            'enabled' => (bool) $request->enabled ?? false,
            'default_password' => $request->has('default_password') ? 0 : $user->default_password,
            'zone_id' => (int) $request->zone_id ?? null,
        ]);
        $user->syncRoles($request->roles);
        $user->syncPermissions($request->permissions);
        return redirect()->route('users.list');
    }

    public function resetPassword(Request $request, $user_id)
    {
        $request->validate([
            'default_password' => 'boolean',
        ]);

        $user = User::findOrFail($user_id);
        $user->update([
            'default_password' => true,
            'password' => Hash::make(Str::password(16, true, true, true, false)),
        ]);

        return redirect()->route('users.detail', $user_id)->with('success', 'Contraseña restablecida exitosamente');
    }

    public function getPermissionRol(Request $request, $roles_id='')
    {
        $permissions = [];
        $result = [];
        foreach (explode(',', $roles_id) as $role_id) {
            $role = Role::findOrFail($role_id);
            if($role->permissions->all()){
                $result = array_merge($role->permissions->all(), $permissions);
            }
        }
        return $result;
    }

    public function getPermissions()
    {
        $permissions = Permission::all();
        return Inertia::render('Users/Permissions', ['permissions' => $permissions]);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $permission = Permission::make(['guard_name' => 'web','name' => $request->name]); 
        $permission->saveOrFail();
        $roleAdministrador = Role::whereIn('name', ['Administrador', 'TI'])->firstOrFail();
        $roleAdministrador->givePermissionTo($permission);

        return redirect('permissions');
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $permission = Permission::findOrFail($request->id);
        $permission->update(['guard_name' => 'web','name' => $request->name]);
        return redirect('permissions');
    }

    public function getRoles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return Inertia::render('Users/Roles', ['roles' => $roles, 'permissions' => $permissions]);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'required',
        ]);
        $role = Role::make(['guard_name' => 'web','name' => $request->name]); 
        $role->saveOrFail();
        $role->syncPermissions($request->permissions);
        return redirect('roles');
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $role = Role::findOrFail($request->id);
        $role->update(['guard_name' => 'web','name' => $request->name]);
        $role->syncPermissions($request->permissions);
        return redirect('roles');
    }
}
