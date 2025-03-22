<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all();
        $roles = Role::all();
        $boss = User::select('user_id', 'name')->whereHas('roles', function ($query) {
            $query->where('name', 'Subdirector');
        })->get();
        return Inertia::render('Users/UsersList', ['users' => $users, 'roles' => $roles, 'boss' => $boss]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:' . User::class,
            // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);


        $user = User::create([
            'username' => (string) $request->username,
            'name' => (string) $request->name,
            'password' => Hash::make('PraisSecret'),
            'boss_user' => (int) $request->boss_user,
            'enabled' => (bool) $request->enabled,
            'location_id' => (int) $request->location_id,
        ]);

        $user->syncRoles($request->role_id);
        

        event(new Registered($user));
        return redirect()->route('users.list');
    }

    public function detailUser($user_id)
    {
        $user = User::with('roles', 'permissions')->findOrFail($user_id);
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        if ($user) {
            return Inertia::render('Users/UserDetail', ['user' => $user, 'roles' => $roles, 'permissions' => $permissions]);
        } else {
            return redirect()->route('users.list');
        }
    }

    public function updateUserRolePermission(Request $request, $user_id)
    {
        $request->validate([
            'roles' => 'required',
        ]);

        $user = User::findOrFail($user_id);
        $user->syncRoles($request->roles);
        $user->syncPermissions($request->permissions);
        return redirect()->route('users.list');
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
        $roleAdministrador = Role::where('name', 'Administrador')->firstOrFail();
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
