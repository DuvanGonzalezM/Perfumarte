<?php

namespace App\Console\Commands;

use App\Support\InitialAdminProvisioner;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Arranque del aplicativo sobre una base de datos nueva.
 *
 * Un solo comando, idempotente y seguro de repetir en un despliegue: verifica
 * el entorno, crea el esquema, carga roles y permisos y provisiona la cuenta
 * técnica inicial (rol TI). Si esa cuenta ya existe, no la toca.
 */
class BootstrapApplication extends Command
{
    protected $signature = 'prais:bootstrap
        {--username= : Nombre de usuario de la cuenta TI inicial}
        {--name= : Nombre visible de la cuenta TI inicial}
        {--link : No pide contraseña: crea la cuenta y emite un enlace de activación firmado}
        {--from-env : Toma la contraseña de PRAIS_ADMIN_PASSWORD en lugar de pedirla por teclado}
        {--keep-env : No borra PRAIS_ADMIN_PASSWORD del archivo .env al terminar}
        {--skip-migrations : No ejecuta las migraciones}
        {--force : Continúa aunque el entorno sea de producción, sin pedir confirmación}';

    protected $description = 'Prepara el aplicativo sobre una base de datos nueva: esquema, roles/permisos y cuenta TI inicial';

    public function handle(InitialAdminProvisioner $provisioner): int
    {
        $this->components->info('Arranque de PRAIS ('.app()->environment().')');

        if (! $this->preflight()) {
            return self::FAILURE;
        }

        if (! $this->confirmProduction()) {
            $this->components->warn('Operación cancelada.');

            return self::FAILURE;
        }

        if (! $this->option('skip-migrations')) {
            $this->components->info('Aplicando migraciones');

            if ($this->call('migrate', ['--force' => true]) !== self::SUCCESS) {
                $this->components->error('Fallaron las migraciones: no se creó ninguna cuenta.');

                return self::FAILURE;
            }
        }

        $this->components->info('Cargando roles y permisos');

        if ($this->call('db:seed', ['--class' => RolePermissionSeeder::class, '--force' => true]) !== self::SUCCESS) {
            $this->components->error('Falló la carga de roles y permisos: no se creó ninguna cuenta.');

            return self::FAILURE;
        }

        return $this->provisionAdmin($provisioner);
    }

    /**
     * Comprobaciones que evitan dejar el aplicativo a medio levantar o
     * levantado con parámetros inseguros.
     */
    private function preflight(): bool
    {
        if (blank(config('app.key'))) {
            $this->components->error('APP_KEY vacía. Ejecute primero: php artisan key:generate');

            return false;
        }

        try {
            DB::connection()->getPdo();
        } catch (Throwable) {
            $this->components->error(sprintf(
                'Sin conexión a la base de datos "%s" en %s:%s. Revise las credenciales DB_* del .env.',
                config('database.connections.'.config('database.default').'.database'),
                config('database.connections.'.config('database.default').'.host'),
                config('database.connections.'.config('database.default').'.port'),
            ));

            return false;
        }

        if (! app()->environment('production')) {
            return true;
        }

        // Parámetros que en producción no son negociables.
        if (config('app.debug')) {
            $this->components->error('APP_DEBUG=true en producción: expone trazas y configuración. Póngalo en false y repita.');

            return false;
        }

        if (! config('session.secure')) {
            $this->components->warn('SESSION_SECURE_COOKIE=false: la cookie de sesión viajará por HTTP. Actívela si el sitio está tras HTTPS.');
        }

        if (str_contains((string) config('app.url'), 'localhost')) {
            $this->components->warn('APP_URL apunta a localhost: los enlaces de activación firmados saldrán con esa URL.');
        }

        return true;
    }

    private function confirmProduction(): bool
    {
        if (! app()->environment('production') || $this->option('force')) {
            return true;
        }

        if (! $this->input->isInteractive()) {
            $this->components->error('Entorno de producción sin terminal interactiva. Repita con --force si está seguro.');

            return false;
        }

        return $this->confirm('Va a ejecutar el arranque contra PRODUCCIÓN. ¿Continuar?', false);
    }

    private function provisionAdmin(InitialAdminProvisioner $provisioner): int
    {
        if ($provisioner->alreadyProvisioned()) {
            $this->components->info('Ya existe una cuenta con rol TI: no se crea ninguna otra.');
            $this->newLine();
            $this->components->bulletList([
                'Si perdió el acceso, restablezca la contraseña desde otra cuenta con permiso "Editar Usuarios".',
                'El arranque puede repetirse sin riesgo: solo aplica migraciones y refresca roles/permisos.',
            ]);

            return self::SUCCESS;
        }

        $username = (string) ($this->option('username') ?: config('prais.bootstrap.username'));
        $name = (string) ($this->option('name') ?: config('prais.bootstrap.name'));

        if ($provisioner->usernameTaken($username)) {
            $this->components->error("Ya existe un usuario '{$username}'. Use --username=otro para la cuenta TI inicial.");

            return self::FAILURE;
        }

        $password = $this->resolvePassword($provisioner, $username);

        if ($password === false) {
            return self::FAILURE;
        }

        try {
            $result = $provisioner->provision(
                $username,
                $name,
                $password,
                // Si la contraseña no la escribió el titular en esta terminal,
                // la cuenta queda obligada a cambiarla en el primer ingreso.
                forceChange: $password === null || $this->option('from-env'),
            );
        } catch (Throwable $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('from-env') && ! $this->option('keep-env')) {
            $this->scrubEnvPassword();
        }

        Log::info('Cuenta TI inicial creada por prais:bootstrap', [
            'username' => $username,
            'user_id' => $result['user']->user_id,
        ]);

        // El enlace solo tiene sentido cuando nadie conoce la contraseña.
        $this->reportSuccess(
            $username,
            $password === null ? $result['activation_url'] : null,
            $result['user']->default_password,
        );

        return self::SUCCESS;
    }

    /**
     * Obtiene la contraseña inicial.
     *
     * @return string|null|false null = generar aleatoria y emitir enlace;
     *                           false = error, abortar.
     */
    private function resolvePassword(InitialAdminProvisioner $provisioner, string $username): string|null|false
    {
        if ($this->option('link')) {
            return null;
        }

        if ($this->option('from-env')) {
            $password = (string) config('prais.bootstrap.password');

            if ($password === '') {
                $this->components->error('PRAIS_ADMIN_PASSWORD está vacía. Defínala en el .env o use --link.');

                return false;
            }

            if ($errors = $provisioner->passwordErrors($password, $username)) {
                $this->components->error('La contraseña de PRAIS_ADMIN_PASSWORD no cumple la política:');
                $this->components->bulletList($errors);

                return false;
            }

            return $password;
        }

        if (! $this->input->isInteractive()) {
            // Sin terminal no se puede pedir una contraseña sin dejarla escrita
            // en algún sitio: se cae al enlace de activación firmado.
            $this->components->warn('Sin terminal interactiva: se emitirá un enlace de activación en lugar de pedir contraseña.');

            return null;
        }

        $this->newLine();
        $this->components->info(sprintf(
            'Contraseña para "%s": mínimo %d caracteres, con mayúsculas y minúsculas, números y símbolos.',
            $username,
            config('prais.bootstrap.min_password_length'),
        ));

        for ($intento = 1; $intento <= 3; $intento++) {
            $password = (string) $this->secret('Contraseña (no se muestra en pantalla)');

            if ($errors = $provisioner->passwordErrors($password, $username)) {
                $this->components->error('Contraseña rechazada:');
                $this->components->bulletList($errors);

                continue;
            }

            if ($password !== (string) $this->secret('Confirme la contraseña')) {
                $this->components->error('Las contraseñas no coinciden.');

                continue;
            }

            return $password;
        }

        $this->components->error('Demasiados intentos fallidos.');

        return false;
    }

    /**
     * Vacía PRAIS_ADMIN_PASSWORD en el .env: una vez usada, la contraseña no
     * tiene por qué seguir en texto plano en el disco del servidor.
     */
    private function scrubEnvPassword(): void
    {
        $path = app()->environmentFilePath();

        if (! is_file($path) || ! is_writable($path)) {
            $this->components->warn('No se pudo limpiar PRAIS_ADMIN_PASSWORD del .env: bórrela manualmente.');

            return;
        }

        $contents = (string) file_get_contents($path);
        $scrubbed = preg_replace('/^PRAIS_ADMIN_PASSWORD=.*$/m', 'PRAIS_ADMIN_PASSWORD=', $contents);

        if ($scrubbed === null || $scrubbed === $contents) {
            $this->components->warn('No se pudo limpiar PRAIS_ADMIN_PASSWORD del .env: bórrela manualmente.');

            return;
        }

        file_put_contents($path, $scrubbed);
        $this->components->info('PRAIS_ADMIN_PASSWORD se borró del .env.');
    }

    private function reportSuccess(string $username, ?string $activationUrl, bool $mustChange): void
    {
        $this->newLine();
        $this->components->info("Cuenta TI creada: {$username}");

        if ($activationUrl === null) {
            $this->components->bulletList(array_filter([
                'Ingrese en '.config('app.url').' con la contraseña que acaba de definir.',
                $mustChange ? 'El sistema le exigirá cambiarla en el primer ingreso.' : null,
                'La contraseña no quedó registrada en ningún archivo ni en los logs.',
            ]));

            return;
        }

        $this->newLine();
        $this->line('  Enlace de activación (válido '.config('prais.bootstrap.activation_ttl_hours').' horas, un solo uso):');
        $this->line('  '.$activationUrl);
        $this->newLine();
        $this->components->bulletList([
            'Entréguelo al titular por un canal seguro; no se volverá a mostrar.',
            'Hasta que fije su contraseña, la cuenta no puede entrar a ningún módulo.',
        ]);
    }
}
