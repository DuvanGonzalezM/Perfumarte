<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Http;

// LoginRequest exige captcha_token y la regla Recaptcha consulta a Google.
beforeEach(function () {
    Http::fake(['*' => Http::response(['success' => true], 200)]);
});

/*
 * La versión anterior de este archivo autenticaba con `email`, campo que la
 * tabla `users` no tiene: las cuatro pruebas fallaban con "Unknown column".
 * El sistema autentica por `username`.
 */

test('la pantalla de login se renderiza', function () {
    $this->get('/login')->assertStatus(200);
});

test('un usuario puede autenticarse con su username y contraseña', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
        'captcha_token' => 'test',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('no se autentica con una contraseña incorrecta', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'contrasena-incorrecta',
        'captcha_token' => 'test',
    ]);

    $this->assertGuest();
});

test('un usuario puede cerrar sesión', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});
