<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PasswordController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/UpdatePassword');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->default_password = false;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Contraseña actualizada exitosamente');
    }
}
