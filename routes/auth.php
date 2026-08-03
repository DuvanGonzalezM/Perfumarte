<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

/*
 * El cambio de contraseña predeterminada queda fuera del grupo 'guest': lo
 * alcanzan tanto el titular autenticado como quien llega con un enlace de
 * activación firmado. El acceso lo decide EnsurePasswordChangeAccess.
 */
Route::middleware(['password.change.access', 'throttle:6,1'])->group(function () {
    Route::get('change-password/{username}', [PasswordChangeController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('change-password/{username}', [PasswordChangeController::class, 'changePassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
