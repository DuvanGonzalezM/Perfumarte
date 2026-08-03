<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reconcilia el esquema con lo que el código realmente usa.
 *
 * Cinco columnas se leen y escriben desde los controladores y ninguna
 * migración las creaba, de modo que `php artisan migrate:fresh` producía una
 * base de datos sobre la que la aplicación no funciona:
 *
 *  - warehouses.price5          Warehouse::$fillable, SaleController:76 (precio
 *                               de la venta de 5 ml; sin la columna se factura 0)
 *  - audit_cash.cash_register_id AuditController:84
 *  - products.name              LocationsController:180, SaleController:228
 *  - sales.location_id          CashRegisterController:70 (sin ella el listado
 *                               de ventas por sede sale siempre vacío)
 *
 * Además, inventories.position se declaró NOT NULL sin valor por defecto y
 * ninguno de los diez Inventory::create() del proyecto la envía: con
 * 'strict' => true toda creación de inventario falla en una base nueva.
 *
 * Las comprobaciones hasColumn() hacen la migración idempotente: en el entorno
 * de producción, cuyo esquema fue alterado fuera del control de migraciones,
 * las columnas que ya existan se omiten sin error.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('warehouses', 'price5')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->integer('price5')->nullable()->default(0)->after('name');
            });
        }

        if (! Schema::hasColumn('audit_cash', 'cash_register_id')) {
            Schema::table('audit_cash', function (Blueprint $table) {
                $table->foreignId('cash_register_id')
                    ->nullable()
                    ->after('id_audits')
                    ->constrained('cash_registers', 'cash_register_id')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('products', 'name')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('name')->nullable()->after('reference');
            });

            // Los productos existentes toman la referencia comercial como
            // nombre para que las vistas de venta y arqueo no salgan en blanco.
            DB::table('products')->whereNull('name')->update([
                'name' => DB::raw('commercial_reference'),
            ]);
        }

        if (! Schema::hasColumn('sales', 'location_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->foreignId('location_id')
                    ->nullable()
                    ->after('cash_register_id')
                    ->constrained('locations', 'location_id')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();

                $table->index('location_id');
            });
        }

        /*
         * SoftDeletes en locations.
         *
         * DELETE /sedes/delete/{location_id} ejecutaba Location::destroy(), y
         * las 43 claves foráneas del proyecto son ON DELETE CASCADE sin una
         * sola excepción. Una única petición HTTP arrasaba:
         *
         *   locations → cash_registers → sales → sale_details
         *   locations → audits → audit_cash + audit_inventory
         *   locations → warehouses → inventories → sale_details,
         *               dispatches_detail, requests_detail, audit_inventory,
         *               consumables, damage_return_detail
         *   locations → requests → requests_detail
         *
         * Ninguna de esas tablas tiene SoftDeletes. Marcar la sede como
         * eliminada en lugar de borrarla corta la cascada en el origen.
         */
        if (! Schema::hasColumn('locations', 'deleted_at')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        /*
         * reports no registra quién generó cada informe: no hay trazabilidad
         * sobre la descarga de información financiera de toda la compañía.
         */
        if (! Schema::hasColumn('reports', 'user_id')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('report_id')
                    ->constrained('users', 'user_id')
                    ->cascadeOnUpdate()
                    ->nullOnDelete();
            });
        }

        // ->change() exige doctrine/dbal, que este proyecto no instala.
        DB::statement('ALTER TABLE inventories MODIFY position VARCHAR(255) NULL');

        /*
         * inventories.quantity pasa de INT a DECIMAL(12,2).
         *
         * SaleController calcula el descuento como
         * (quantity * units) * 0.5, que para una venta de 5 ml da 2.5. Sobre
         * una columna entera MySQL trunca en silencio y el stock pierde media
         * unidad en cada venta fraccionada. El importe en pesos sí es entero y
         * se deja como está: el COP no maneja centavos en retail.
         */
        DB::statement('ALTER TABLE inventories MODIFY quantity DECIMAL(12,2) NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropIndex(['location_id']);
            $table->dropColumn('location_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('audit_cash', function (Blueprint $table) {
            $table->dropForeign(['cash_register_id']);
            $table->dropColumn('cash_register_id');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn('price5');
        });

        DB::statement('ALTER TABLE inventories MODIFY position VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE inventories MODIFY quantity INT NOT NULL');
    }
};
