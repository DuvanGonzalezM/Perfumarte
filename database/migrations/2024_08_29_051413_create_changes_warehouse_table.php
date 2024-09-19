<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('changes_warehouse', function (Blueprint $table) {
            $table->id('change_warehouse_id');
            $table->foreignId('inventory_id')->constrained('inventories', 'inventory_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('quantity');
            $table->boolean('enable')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('changes_warehouse');
    }
};
