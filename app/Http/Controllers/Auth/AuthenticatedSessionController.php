<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
        $user = User::whit('roles')->where('username', $request->username)->firstOrFail();
        if ($user && $user->default_password) {
            return redirect()->route('password.change', ['username' => $user->username]);
        }
        if($user && $user->hasRole('Asesor comercial') && $user->enabled == 0){
            return redirect()->route('login')->with('error', 'El usuario '.$user->username.' no esta habilitado');
        }
        $request->authenticate();

        $request->session()->regenerate();
        $request->session()->put('user_id', $request->user()->user_id);
        
        if ($request->user()->hasRole('Asesor comercial')) {
            return redirect()->intended(RouteServiceProvider::INVENTORY_ADVISOR);
        }
        if ($request->user()->hasRole('Control Gerencia')) {
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
