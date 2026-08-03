<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
