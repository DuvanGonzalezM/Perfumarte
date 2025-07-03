<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Inertia\Response;



class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $roles = Role::all();
        $boss = User::select('user_id', 'name')->whereHas('roles', function ($query) {
            $query->where('name', 'Subdirector');
        })->get();
        return Inertia::render('Auth/Register', ['roles' => $roles, 'boss' => $boss]);

    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:' . User::class,
            'name' => 'required|string|max:255',
            'boss_user' => 'nullable|integer|exists:users,user_id',
            'enabled' => 'required|boolean',
            'location_id' => 'required|integer|exists:locations,location_id',
            // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);


        $user = User::create([
            'username' => (string) $request->username,
            'name' => (string) $request->name,
            'password' => Hash::make(Str::password(16, true, true, true, false)),
            'boss_user' => (int) $request->boss_user,
            'enabled' => (bool) $request->enabled,
            'location_id' => (int) $request->location_id,
        ]);

        $user->syncRoles($request->role_id);
        

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
