<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Restricciones e índices que faltaban.
 *
 *  - users.username no tenía unique a nivel de base de datos: la unicidad
 *    dependía solo de la regla de validación, que no protege frente a dos
 *    peticiones concurrentes ni frente a una escritura directa.
 *  - boss_user y zone_id se declararon como string() con constrained()
 *    encadenado, que sobre una columna de texto es inerte: no se creó ninguna
 *    clave foránea real.
 *  - Cinco tablas se filtran por columnas sin índice.
 *
 * Todas las operaciones están guardadas para poder ejecutarse sobre una base
 * con datos sin romper el despliegue: si un dato existente impide aplicar una
 * restricción, se informa y se continúa en vez de abortar la migración
 * completa.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->addUniqueUsername();
        $this->convertToRealForeignKey('users', 'boss_user', 'users', 'user_id');
        $this->convertToRealForeignKey('users', 'zone_id', 'zones', 'zone_id');
        $this->addMissingIndexes();
    }

    private function addUniqueUsername(): void
    {
        $duplicates = DB::table('users')
            ->select('username')
            ->groupBy('username')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        if ($duplicates > 0) {
            echo "AVISO: existen {$duplicates} username duplicados; no se aplica el índice unique en users.username. Depúrelos y vuelva a ejecutar.\n";

            return;
        }

        if (! $this->indexExists('users', 'users_username_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('username');
            });
        }
    }

    /**
     * Convierte una columna de texto que guarda un identificador en una clave
     * foránea real.
     *
     * Los valores que no apuntan a ninguna fila existente —incluido el 0 que
     * escribía el patrón `(int) $request->campo ?? null`— pasan a NULL antes de
     * crear la restricción.
     */
    private function convertToRealForeignKey(string $table, string $column, string $referencedTable, string $referencedColumn): void
    {
        if ($this->foreignKeyExists($table, "{$table}_{$column}_foreign")) {
            return;
        }

        DB::statement("
            UPDATE `{$table}`
            SET `{$column}` = NULL
            WHERE `{$column}` IS NOT NULL
              AND `{$column}` NOT IN (SELECT `{$referencedColumn}` FROM (SELECT `{$referencedColumn}` FROM `{$referencedTable}`) AS ref)
        ");

        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` BIGINT UNSIGNED NULL");

        Schema::table($table, function (Blueprint $blueprint) use ($column, $referencedTable, $referencedColumn) {
            $blueprint->foreign($column)
                ->references($referencedColumn)
                ->on($referencedTable)
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    private function addMissingIndexes(): void
    {
        $indexes = [
            'suppliers' => [['status'], ['name']],
            'purchase_orders' => [['created_at']],
            'reports' => [['created_at'], ['type_report']],
            'zones' => [['zone_name']],
            'products' => [['category', 'status'], ['reference']],
        ];

        foreach ($indexes as $table => $columns) {
            foreach ($columns as $definition) {
                $name = $table.'_'.implode('_', $definition).'_index';

                if ($this->indexExists($table, $name)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $blueprint) use ($definition, $name) {
                    $blueprint->index($definition, $name);
                });
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        return DB::table('information_schema.table_constraints')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('constraint_name', $constraint)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_zone_id_foreign');
            $table->dropForeign('users_boss_user_foreign');
            $table->dropUnique('users_username_unique');
        });

        DB::statement('ALTER TABLE `users` MODIFY `boss_user` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `users` MODIFY `zone_id` VARCHAR(255) NULL');

        foreach ([
            'suppliers' => ['suppliers_status_index', 'suppliers_name_index'],
            'purchase_orders' => ['purchase_orders_created_at_index'],
            'reports' => ['reports_created_at_index', 'reports_type_report_index'],
            'zones' => ['zones_zone_name_index'],
            'products' => ['products_category_status_index', 'products_reference_index'],
        ] as $table => $names) {
            Schema::table($table, function (Blueprint $blueprint) use ($names) {
                foreach ($names as $name) {
                    $blueprint->dropIndex($name);
                }
            });
        }
    }
};
