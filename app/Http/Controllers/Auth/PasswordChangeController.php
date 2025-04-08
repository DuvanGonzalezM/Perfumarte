<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class PasswordChangeController extends Controller
{
    /**
     * Muestra la vista para cambiar la contraseña.
     */
    public function showChangePasswordForm($username)
    {
        return Inertia::render('Auth/ChangePassword', [
            'username' => $username,
        ]);
    }

    /**
     * Maneja la solicitud de cambio de contraseña.
     */
    public function changePassword(Request $request, $username)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::find(Auth::id());

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->default_password = false;
        $user->save();

        return redirect()->route('login');
    }
}
