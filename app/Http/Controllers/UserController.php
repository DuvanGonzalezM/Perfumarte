<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::all();
        return Inertia::render('Users/UsersList', ['users' => $users]);
    }

    public function detailUser($user_id)
    {
        $user = User::findOrFail($user_id);
        $roles = Role::all();
        $permissions = Permission::all();

        if ($user) {
            return Inertia::render('Users/UserDetail', ['user' => $user, 'roles' => $roles, 'permissions' => $permissions]);
        } else {
            return redirect()->route('users.list');
        }
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
        $permissions = Permission::all();
        return Inertia::render('Users/Permissions', ['permissions' => $permissions]);
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Permission::where('id', $request->id)->update(['guard_name' => 'web','name' => $request->name]);
        $permissions = Permission::all();
        return Inertia::render('Users/Permissions', ['permissions' => $permissions]);
    }
}
