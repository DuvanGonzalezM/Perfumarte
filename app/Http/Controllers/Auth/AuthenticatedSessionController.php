<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        $error = $request->session()->get('error') ?? '';
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
            'error' => $error
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        /*
         * Toda comprobación sobre el estado de la cuenta va DESPUÉS de
         * authenticate(). Antes se hacían antes, y la de default_password
         * redirigía al formulario de cambio de contraseña sin validar la
         * credencial: bastaba enviar un username válido con cualquier
         * contraseña para obtener la URL con la que tomar control de la
         * cuenta. Las cuentas con contraseña predeterminada se activan ahora
         * mediante el enlace firmado que emite el administrador.
         */
        $request->authenticate();

        $user = $request->user()->loadMissing('roles');

        if ($user->hasRole('Asesor comercial') && $user->enabled == 0) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'El usuario '.$user->username.' no esta habilitado');
        }

        $request->session()->regenerate();
        $request->session()->put('user_id', $request->user()->user_id);
        
        if ($request->user()->hasRole('Asesor comercial')) {
            return redirect()->intended(RouteServiceProvider::INVENTORY_ADVISOR);
        }
        if ($request->user()->hasRole('Control gerencia')) {
            return redirect()->intended(RouteServiceProvider::MONITORING_DASHBOARD);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $error = $request->session()->get('error') ?? '';
        $request->session()->invalidate();
        
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('error', $error);
    }
}

