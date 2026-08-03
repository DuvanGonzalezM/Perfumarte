<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class PasswordChangeController extends Controller
{
    private const ACTION_URL_TTL_MINUTES = 30;

    /**
     * Muestra la vista para cambiar la contraseña.
     */
    public function showChangePasswordForm(string $username)
    {
        return Inertia::render('Auth/ChangePassword', [
            'username' => $username,
            'actionUrl' => $this->actionUrl($username),
        ]);
    }

    /**
     * Maneja la solicitud de cambio de contraseña.
     */
    public function changePassword(Request $request, string $username)
    {
        $this->validate($request, [
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('username', $username)
            ->where('enabled', true)
            ->where('default_password', true)
            ->first();

        if (! $user) {
            return back()->withErrors([
                'password' => 'La cuenta no admite el cambio de contraseña por esta vía.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->default_password = false;
        $user->save();

        return redirect()->route('login')
            ->with('status', 'Contraseña actualizada. Ya puede iniciar sesión.');
    }

    private function actionUrl(string $username): string
    {
        if (Auth::check() && Auth::user()->username === $username) {
            return route('password.update', $username);
        }

        return URL::temporarySignedRoute(
            'password.update',
            now()->addMinutes(self::ACTION_URL_TTL_MINUTES),
            ['username' => $username]
        );
    }
}
