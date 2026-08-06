<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function ti(): User
{
    $ti = User::factory()->create();
    $ti->assignRole('TI');

    return $ti;
}

function usuarioConRol(string $rol): User
{
    $user = User::factory()->create();
    $user->assignRole($rol);

    return $user;
}

function rolId(string $nombre): int
{
    return Role::where('name', $nombre)->first()->id;
}

test('un asesor comercial no se puede crear sin jefe', function () {
    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567890',
            'name' => 'Asesor sin jefe',
            'role_id' => rolId('Asesor comercial'),
        ])
        ->assertSessionHasErrors('boss_user');

    expect(User::where('username', '1234567890')->exists())->toBeFalse();
});

test('un asesor comercial no se puede colgar de un subdirector', function () {
    $subdirector = usuarioConRol('Subdirector');

    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567891',
            'name' => 'Asesor mal colgado',
            'role_id' => rolId('Asesor comercial'),
            'boss_user' => $subdirector->user_id,
        ])
        ->assertSessionHasErrors('boss_user');

    expect(User::where('username', '1234567891')->exists())->toBeFalse();
});

test('un asesor comercial se crea con un supervisor como jefe', function () {
    $supervisor = usuarioConRol('Supervisor');

    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567892',
            'name' => 'Asesor correcto',
            'role_id' => rolId('Asesor comercial'),
            'boss_user' => $supervisor->user_id,
        ])
        ->assertSessionHasNoErrors();

    expect(User::where('username', '1234567892')->first()?->boss_user)
        ->toBe($supervisor->user_id);
});

test('un supervisor depende de un subdirector, no de otro supervisor', function () {
    $otroSupervisor = usuarioConRol('Supervisor');

    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567893',
            'name' => 'Supervisor mal colgado',
            'role_id' => rolId('Supervisor'),
            'boss_user' => $otroSupervisor->user_id,
        ])
        ->assertSessionHasErrors('boss_user');
});

test('un rol sin dependencia se crea sin jefe', function () {
    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567894',
            'name' => 'Subdirector suelto',
            'role_id' => rolId('Subdirector'),
        ])
        ->assertSessionHasNoErrors();

    expect(User::where('username', '1234567894')->first()?->boss_user)->toBeNull();
});

test('un jefe eliminado ya no sirve como jefe', function () {
    $supervisor = usuarioConRol('Supervisor');
    $supervisor->delete();

    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567895',
            'name' => 'Asesor huerfano',
            'role_id' => rolId('Asesor comercial'),
            'boss_user' => $supervisor->user_id,
        ])
        ->assertSessionHasErrors('boss_user');
});

test('nadie puede quedar como su propio jefe al cambiarle los roles', function () {
    $supervisor = usuarioConRol('Supervisor');

    $this->actingAs(ti())
        ->post('/users/'.$supervisor->user_id, [
            'username' => $supervisor->username,
            'name' => $supervisor->name,
            'roles' => [rolId('Supervisor')],
            'boss_user' => $supervisor->user_id,
        ])
        ->assertSessionHasErrors('boss_user');
});

test('una cuenta sin pregunta de caja nace habilitada y puede activarse', function () {
    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567897',
            'name' => 'Subdirector nuevo',
            'role_id' => rolId('Subdirector'),
        ])
        ->assertSessionHasNoErrors();

    $creado = User::where('username', '1234567897')->first();

    expect($creado->enabled)->toBeTrue()
        ->and($creado->default_password)->toBeTrue();

    // Es exactamente lo que consulta PasswordChangeController para aceptar el
    // enlace de activación.
    expect(User::where('username', '1234567897')
        ->where('enabled', true)
        ->where('default_password', true)
        ->exists())->toBeTrue();
});

test('un asesor comercial sin caja queda deshabilitado, como antes', function () {
    $supervisor = usuarioConRol('Supervisor');

    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567898',
            'name' => 'Asesor sin caja',
            'role_id' => rolId('Asesor comercial'),
            'boss_user' => $supervisor->user_id,
            'enabled' => false,
        ])
        ->assertSessionHasNoErrors();

    expect(User::where('username', '1234567898')->first()->enabled)->toBeFalse();
});

test('un asesor comercial con caja queda habilitado', function () {
    $supervisor = usuarioConRol('Supervisor');

    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567899',
            'name' => 'Asesor con caja',
            'role_id' => rolId('Asesor comercial'),
            'boss_user' => $supervisor->user_id,
            'enabled' => true,
        ])
        ->assertSessionHasNoErrors();

    expect(User::where('username', '1234567899')->first()->enabled)->toBeTrue();
});

test('al crear un usuario ya no se le asigna la sede 1 a ciegas', function () {
    $supervisor = usuarioConRol('Supervisor');

    $this->actingAs(ti())
        ->post('/users', [
            'username' => '1234567896',
            'name' => 'Asesor sin sede',
            'role_id' => rolId('Asesor comercial'),
            'boss_user' => $supervisor->user_id,
        ])
        ->assertSessionHasNoErrors();

    expect(User::where('username', '1234567896')->first()?->location_id)->toBeNull();
});
