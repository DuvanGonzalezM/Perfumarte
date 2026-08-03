<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function administrador(): User
{
    $user = User::factory()->create();
    $user->assignRole('Administrador');

    return $user;
}

function usuarioTi(): User
{
    $user = User::factory()->create(['password' => Hash::make('secreto-del-ti')]);
    $user->assignRole('TI');

    return $user;
}

test('un administrador no puede restablecer la contraseña de una cuenta TI', function () {
    $ti = usuarioTi();

    $this->actingAs(administrador())
        ->post('/users/'.$ti->user_id.'/reset-password', [])
        ->assertForbidden();

    expect(Hash::check('secreto-del-ti', $ti->fresh()->password))->toBeTrue();
    expect($ti->fresh()->default_password)->toBeFalse();
    expect(session('activation_url'))->toBeNull();
});

test('un administrador no puede editar los datos de una cuenta TI', function () {
    $ti = usuarioTi();

    $this->actingAs(administrador())
        ->put('/users/edit/'.$ti->user_id, [
            'username' => 'secuestrada',
            'name' => 'Secuestrada',
        ])
        ->assertForbidden();

    expect($ti->fresh()->username)->not->toBe('secuestrada');
});

test('un administrador no puede eliminar una cuenta TI', function () {
    $ti = usuarioTi();

    $this->actingAs(administrador())
        ->delete('/users/delete/'.$ti->user_id)
        ->assertForbidden();

    expect($ti->fresh()->deleted_at)->toBeNull();
});

test('un administrador no puede degradar el rol de una cuenta TI', function () {
    $ti = usuarioTi();

    $this->actingAs(administrador())
        ->post('/users/'.$ti->user_id, [
            'roles' => [Role::where('name', 'Usuario')->first()->id],
            'username' => $ti->username,
            'name' => $ti->name,
        ])
        ->assertForbidden();

    expect($ti->fresh()->hasRole('TI'))->toBeTrue();
});

test('un administrador no puede otorgar permisos que el mismo no posee', function () {
    $victima = User::factory()->create();
    $victima->assignRole('Usuario');

    $crearRoles = Permission::where('name', 'Crear Roles')->first();

    $this->actingAs(administrador())
        ->post('/users/'.$victima->user_id, [
            'roles' => [Role::where('name', 'Usuario')->first()->id],
            'permissions' => [$crearRoles->id],
            'username' => $victima->username,
            'name' => $victima->name,
        ])
        ->assertSessionHasErrors('permissions');

    expect($victima->fresh()->hasPermissionTo('Crear Roles'))->toBeFalse();
});

test('un TI si puede administrar otra cuenta TI', function () {
    $ti = usuarioTi();
    $otroTi = User::factory()->create();
    $otroTi->assignRole('TI');

    $this->actingAs($ti)
        ->post('/users/'.$otroTi->user_id.'/reset-password', [])
        ->assertRedirect();

    expect($otroTi->fresh()->default_password)->toBeTrue();
});

test('una cuenta eliminada no puede iniciar sesion', function () {
    Http::fake(['*' => Http::response(['success' => true], 200)]);

    $user = User::factory()->create(['password' => Hash::make('Password123!')]);
    $user->assignRole('Administrador');
    $user->delete();

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'Password123!',
        'captcha_token' => 'test',
    ])->assertSessionHasErrors('username');

    $this->assertGuest();
});
