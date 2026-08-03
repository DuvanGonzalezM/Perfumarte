<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Deja la base de datos en un estado desde el que la aplicación arranca.
     *
     * Ambos seeders son idempotentes: pueden ejecutarse sobre una base con
     * datos sin revocar permisos ni pisar usuarios existentes.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
