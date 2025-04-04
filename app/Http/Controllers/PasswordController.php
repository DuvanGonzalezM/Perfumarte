<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->first_login = false;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Contraseña actualizada exitosamente');
    }
}