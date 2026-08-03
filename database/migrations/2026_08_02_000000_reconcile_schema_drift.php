<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        if (! Schema::hasColumn('locations', 'deleted_at')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

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

        DB::statement('ALTER TABLE inventories MODIFY position VARCHAR(255) NULL');

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
