<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::fake(['*' => Http::response(['success' => true], 200)]);
});

test('la pantalla de login se renderiza', function () {
    if (! file_exists(public_path('build/manifest.json'))) {
        $this->markTestSkipped('Ejecute `make compile` antes: faltan los assets compilados.');
    }

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
