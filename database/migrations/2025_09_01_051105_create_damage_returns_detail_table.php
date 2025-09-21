<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('damage_return_detail', function (Blueprint $table) {
            $table->id('damage_return_detail_id');
            $table->foreignId('damage_return_id')->constrained('damage_returns', 'damage_return_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories', 'inventory_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses', 'warehouse_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('damage_quantity');
            $table->boolean('received');
            $table->string('observations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_return_detail');
    }
};
