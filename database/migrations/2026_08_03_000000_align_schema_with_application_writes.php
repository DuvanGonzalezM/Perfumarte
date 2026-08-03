<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('products', 'dependents')) {
            DB::statement('ALTER TABLE `products` MODIFY `dependents` VARCHAR(255) NULL');
        }

        if (Schema::hasColumn('dispatches_detail', 'returned_quantity')) {
            DB::statement('UPDATE `dispatches_detail` SET `returned_quantity` = 0 WHERE `returned_quantity` IS NULL');
            DB::statement('ALTER TABLE `dispatches_detail` MODIFY `returned_quantity` INT NOT NULL DEFAULT 0');
        }

        if (Schema::hasColumn('dispatches_detail', 'observations')) {
            DB::statement('ALTER TABLE `dispatches_detail` MODIFY `observations` VARCHAR(255) NULL');
        }

        if (Schema::hasColumn('requests', 'location_id')) {
            DB::statement('ALTER TABLE `requests` MODIFY `location_id` BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        DB::statement("UPDATE `products` SET `dependents` = '' WHERE `dependents` IS NULL");
        DB::statement('ALTER TABLE `products` MODIFY `dependents` VARCHAR(255) NOT NULL');

        DB::statement('ALTER TABLE `dispatches_detail` MODIFY `returned_quantity` INT NOT NULL');

        DB::statement("UPDATE `dispatches_detail` SET `observations` = '' WHERE `observations` IS NULL");
        DB::statement('ALTER TABLE `dispatches_detail` MODIFY `observations` VARCHAR(255) NOT NULL');
    }
};
