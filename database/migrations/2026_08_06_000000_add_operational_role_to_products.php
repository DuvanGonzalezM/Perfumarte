<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Da identidad propia a los productos que la operación trata de forma especial.
 *
 * Hasta ahora se reconocían por id fijo escrito en el código: el disolvente era
 * "el producto 2", la bolsa de regalo "la 372", los envases de 50 ml "la 388 a
 * la 391". Cualquier recarga del catálogo rompía ventas y laboratorio en
 * silencio. Con esta columna el rol viaja con el dato y el código pregunta por
 * él en vez de por un número.
 *
 * El backfill usa esos mismos ids una última vez, para que las bases ya
 * existentes queden marcadas sin intervención manual.
 */
return new class extends Migration
{
    /**
     * Ids históricos => rol operativo. Es la única parte de la aplicación donde
     * estos números vuelven a aparecer, y solo se leen durante la migración.
     */
    private const LEGACY_IDS = [
        1 => 'dipropylene',
        2 => 'solvent',
        372 => 'gift_bag',
        385 => 'container_30',
        386 => 'container_30',
        388 => 'container_50',
        389 => 'container_50',
        390 => 'container_50',
        391 => 'container_50',
        392 => 'container_100',
        393 => 'container_100',
        394 => 'container_100',
    ];

    public function up(): void
    {
        if (! Schema::hasColumn('products', 'operational_role')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('operational_role')->nullable()->after('category')->index();
            });
        }

        foreach (self::LEGACY_IDS as $productId => $role) {
            DB::table('products')
                ->where('product_id', $productId)
                ->whereNull('operational_role')
                ->update(['operational_role' => $role]);
        }

        // Respaldo por si los ids no coinciden con los de esta base: la bolsa
        // de regalo también se identificaba por el texto exacto de reference
        // (SaleController).
        DB::table('products')
            ->where('reference', 'Bolsa de regalo')
            ->whereNull('operational_role')
            ->update(['operational_role' => 'gift_bag']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'operational_role')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['operational_role']);
                $table->dropColumn('operational_role');
            });
        }
    }
};
