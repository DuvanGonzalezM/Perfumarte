<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Crea la cuenta técnica inicial.
 *
 * Sin ella, una instalación desde cero queda sin ningún usuario y no hay forma
 * de entrar al sistema para crear el primero.
 *
 * La cuenta nace con `default_password = true`, así que CheckFirstLogin obliga
 * a cambiar la contraseña en el primer ingreso.
 *
 * La contraseña se toma de ADMIN_SEED_PASSWORD; si no está definida se genera
 * una aleatoria y se imprime en la consola una sola vez.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('ADMIN_SEED_USERNAME', 'admin');

        if (User::where('username', $username)->exists()) {
            $this->command?->warn("El usuario '{$username}' ya existe: no se modifica.");

            return;
        }

        $password = env('ADMIN_SEED_PASSWORD') ?: Str::password(16, true, true, true, false);

        $user = User::create([
            'username' => $username,
            'name' => 'Administrador técnico',
            'password' => Hash::make($password),
            'enabled' => true,
            'default_password' => true,
        ]);

        $user->assignRole('TI');

        $this->command?->info("Usuario inicial creado: {$username}");
        $this->command?->warn("Contraseña temporal: {$password}");
        $this->command?->warn('Cámbiela en el primer ingreso. No volverá a mostrarse.');
    }
}
