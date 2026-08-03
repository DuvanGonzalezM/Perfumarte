<?php

namespace Database\Seeders;

use App\Support\InitialAdminProvisioner;
use Illuminate\Database\Seeder;
use Throwable;

/**
 * Crea la cuenta técnica inicial durante `php artisan db:seed`.
 *
 * El camino recomendado para una base nueva es `php artisan prais:bootstrap`,
 * que pide la contraseña por teclado. Este seeder existe para que `make seed` y
 * `migrate:fresh --seed` dejen el entorno usable: crea la cuenta con una
 * contraseña aleatoria que nadie ve y emite un enlace de activación firmado.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $provisioner = app(InitialAdminProvisioner::class);

        $username = (string) config('prais.bootstrap.username');

        if ($provisioner->alreadyProvisioned()) {
            $this->command?->warn('Ya existe una cuenta con rol TI: no se crea ninguna otra.');

            return;
        }

        if ($provisioner->usernameTaken($username)) {
            $this->command?->warn("El usuario '{$username}' ya existe: no se modifica.");

            return;
        }

        try {
            $result = $provisioner->provision(
                $username,
                (string) config('prais.bootstrap.name'),
                $this->seedPassword($provisioner, $username),
            );
        } catch (Throwable $e) {
            $this->command?->error($e->getMessage());

            return;
        }

        $this->command?->info("Usuario inicial creado: {$username}");

        if ($result['activation_url'] !== null) {
            $this->command?->warn('Active la cuenta con este enlace (válido '.config('prais.bootstrap.activation_ttl_hours').' horas):');
            $this->command?->line($result['activation_url']);
        }
    }

    /**
     * Solo fuera de producción se acepta la contraseña del .env, y aun así debe
     * cumplir la política. En producción la cuenta nace sin contraseña conocida
     * y se activa por enlace firmado.
     */
    private function seedPassword(InitialAdminProvisioner $provisioner, string $username): ?string
    {
        if (app()->environment('production')) {
            return null;
        }

        $password = (string) config('prais.bootstrap.password');

        if ($password === '') {
            return null;
        }

        if ($errors = $provisioner->passwordErrors($password, $username)) {
            $this->command?->warn('PRAIS_ADMIN_PASSWORD no cumple la política; se emitirá un enlace de activación:');

            foreach ($errors as $error) {
                $this->command?->line('  - '.$error);
            }

            return null;
        }

        return $password;
    }
}
