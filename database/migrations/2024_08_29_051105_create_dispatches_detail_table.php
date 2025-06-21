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
        Schema::create('dispatches_detail', function (Blueprint $table) {
            $table->id('dispatchs_detail_id');
            $table->foreignId('dispatch_id')->constrained('dispatches', 'dispatch_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories', 'inventory_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses', 'warehouse_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('dispatched_quantity');
            $table->boolean('received');
            $table->integer('returned_quantity');
            $table->string('observations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches_detail');
    }
};
