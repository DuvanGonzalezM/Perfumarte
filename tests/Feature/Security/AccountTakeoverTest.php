<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Http::fake([
        '*' => Http::response(['success' => true], 200),
    ]);
});

test('un invitado no puede abrir el cambio de contraseña sin enlace firmado', function () {
    $victima = User::factory()->withDefaultPassword()->create();

    $this->get('/change-password/'.$victima->username)->assertForbidden();
});

test('un invitado no puede fijar la contraseña de otra cuenta sin firma', function () {
    $victima = User::factory()->withDefaultPassword()->create([
        'password' => Hash::make('la-original-que-nadie-conoce'),
    ]);

    $this->put('/change-password/'.$victima->username, [
        'password' => 'Atacante123!',
        'password_confirmation' => 'Atacante123!',
    ])->assertForbidden();

    expect(Hash::check('Atacante123!', $victima->fresh()->password))->toBeFalse();
    expect($victima->fresh()->default_password)->toBeTrue();
});

test('el login ya no revela qué cuentas tienen contraseña predeterminada', function () {
    $victima = User::factory()->withDefaultPassword()->create();

    $response = $this->post('/login', [
        'username' => $victima->username,
        'password' => 'cualquier-cosa',
        'captcha_token' => 'test',
    ]);

    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

test('el enlace firmado que emite el administrador sí permite fijar la contraseña', function () {
    $usuario = User::factory()->withDefaultPassword()->create();

    $url = URL::temporarySignedRoute(
        'password.update',
        now()->addHours(72),
        ['username' => $usuario->username]
    );

    $this->put($url, [
        'password' => 'NuevaSegura123!',
        'password_confirmation' => 'NuevaSegura123!',
    ])->assertRedirect(route('login'));

    $usuario->refresh();

    expect(Hash::check('NuevaSegura123!', $usuario->password))->toBeTrue();
    expect($usuario->default_password)->toBeFalse();
});

test('un enlace firmado vencido no sirve', function () {
    $usuario = User::factory()->withDefaultPassword()->create();

    $url = URL::temporarySignedRoute(
        'password.update',
        now()->subMinute(),
        ['username' => $usuario->username]
    );

    $this->put($url, [
        'password' => 'NuevaSegura123!',
        'password_confirmation' => 'NuevaSegura123!',
    ])->assertForbidden();

    expect($usuario->fresh()->default_password)->toBeTrue();
});

test('una cuenta deshabilitada no puede activarse ni con enlace válido', function () {
    $usuario = User::factory()->withDefaultPassword()->disabled()->create();

    $url = URL::temporarySignedRoute(
        'password.update',
        now()->addHour(),
        ['username' => $usuario->username]
    );

    $this->put($url, [
        'password' => 'NuevaSegura123!',
        'password_confirmation' => 'NuevaSegura123!',
    ]);

    expect(Hash::check('NuevaSegura123!', $usuario->fresh()->password))->toBeFalse();
});
