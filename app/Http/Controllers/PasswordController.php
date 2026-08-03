<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PasswordController extends Controller
{
    /**
     * Formulario de cambio de contraseña del usuario autenticado.
     *
     * Antes devolvía view('auth.change-password'), plantilla que no existe:
     * resources/views/ solo contiene app.blade.php, así que la ruta producía
     * un 500 permanente.
     */
    public function show()
    {
        return Inertia::render('Auth/UpdatePassword');
    }

    public function update(Request $request)
    {
        // Antes no exigía la contraseña actual: quien obtuviera una sesión
        // podía fijar una nueva sin conocer la anterior.
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
