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
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('username', $request->username)->whereAnd('enabled', true)->whereAnd('default_password', true)->first();
        if ($user && $user->default_password) {
            return redirect()->route('password.change', ['username' => $user->username]);
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

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
