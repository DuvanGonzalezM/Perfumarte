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
        $this->validate($request, [
            'password' => 'required|confirmed',
        ]);

        $user = User::where('username', $username)->whereAnd('enabled', true)->whereAnd('default_password', true)->first();
        if ($user && $user->default_password) {
    
            $user->password = Hash::make($request->password);
            $user->default_password = false;
            $user->save();
    
        }
        return redirect()->route('login');
    }
}
