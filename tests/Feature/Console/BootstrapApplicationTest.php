<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\PendingCommand;

beforeEach(function () {
    // La regla uncompromised() consulta la lista de contraseñas filtradas.
    Http::fake(['*' => Http::response('', 200)]);
});

/**
 * `--keep-env` es obligatorio en las pruebas: sin él el comando limpiaría la
 * variable en el .env real del repositorio.
 */
function bootstrap(array $opciones = []): PendingCommand
{
    return test()->artisan('prais:bootstrap', array_merge([
        '--skip-migrations' => true,
        '--keep-env' => true,
    ], $opciones));
}

test('crea la cuenta TI inicial con la contraseña entregada por variable de entorno', function () {
    config(['prais.bootstrap.password' => 'Arranque-Seguro-2026!']);

    bootstrap(['--from-env' => true, '--username' => 'ti-inicial'])->assertSuccessful();

    $user = User::where('username', 'ti-inicial')->firstOrFail();

    expect($user->hasRole('TI'))->toBeTrue()
        ->and($user->enabled)->toBeTrue()
        ->and(Hash::check('Arranque-Seguro-2026!', $user->password))->toBeTrue()
        // La conoce quien despliega, no el titular: hay que cambiarla al entrar.
        ->and($user->default_password)->toBeTrue();
});

test('rechaza una contraseña que no cumple la política y no crea ninguna cuenta', function () {
    config(['prais.bootstrap.password' => 'password123']);

    bootstrap(['--from-env' => true, '--username' => 'ti-inicial'])->assertFailed();

    expect(User::where('username', 'ti-inicial')->exists())->toBeFalse();
});

test('rechaza una contraseña que contiene el nombre de usuario', function () {
    config(['prais.bootstrap.password' => 'Ti-Inicial-2026!!']);

    bootstrap(['--from-env' => true, '--username' => 'ti-inicial'])->assertFailed();

    expect(User::where('username', 'ti-inicial')->exists())->toBeFalse();
});

test('con --link la cuenta nace sin contraseña utilizable y obligada a activarse', function () {
    bootstrap(['--link' => true, '--username' => 'ti-inicial'])->assertSuccessful();

    $user = User::where('username', 'ti-inicial')->firstOrFail();

    expect($user->default_password)->toBeTrue()
        ->and($user->hasRole('TI'))->toBeTrue();
});

test('repetir el arranque no crea una segunda cuenta TI', function () {
    bootstrap(['--link' => true, '--username' => 'ti-inicial'])->assertSuccessful();

    config(['prais.bootstrap.password' => 'Otra-Clave-Distinta-2026!']);
    bootstrap(['--from-env' => true, '--username' => 'ti-segundo'])->assertSuccessful();

    expect(User::role('TI')->count())->toBe(1)
        ->and(User::where('username', 'ti-segundo')->exists())->toBeFalse();
});

test('borra PRAIS_ADMIN_PASSWORD del .env después de usarla', function () {
    // Se apunta la aplicación a un .env desechable: el del repositorio no se toca.
    $dir = sys_get_temp_dir().'/prais-bootstrap-'.uniqid();
    mkdir($dir);
    file_put_contents($dir.'/.env', "APP_NAME=Perfumarte\nPRAIS_ADMIN_PASSWORD=Arranque-Seguro-2026!\nDB_HOST=127.0.0.1\n");

    $this->app->useEnvironmentPath($dir);
    config(['prais.bootstrap.password' => 'Arranque-Seguro-2026!']);

    $this->artisan('prais:bootstrap', [
        '--skip-migrations' => true,
        '--from-env' => true,
        '--username' => 'ti-inicial',
    ])->assertSuccessful();

    $contenido = (string) file_get_contents($dir.'/.env');

    expect($contenido)->not->toContain('Arranque-Seguro-2026!')
        ->and($contenido)->toContain("PRAIS_ADMIN_PASSWORD=\n")
        // El resto del archivo queda intacto.
        ->and($contenido)->toContain('DB_HOST=127.0.0.1');

    unlink($dir.'/.env');
    rmdir($dir);
});

test('no crea la cuenta si el nombre de usuario ya está tomado', function () {
    User::factory()->create(['username' => 'ti-inicial']);
    config(['prais.bootstrap.password' => 'Arranque-Seguro-2026!']);

    bootstrap(['--from-env' => true, '--username' => 'ti-inicial'])->assertFailed();

    expect(User::where('username', 'ti-inicial')->first()->hasRole('TI'))->toBeFalse();
});
