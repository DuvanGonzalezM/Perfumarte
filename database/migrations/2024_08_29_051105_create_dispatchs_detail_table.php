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
        Schema::create('dispatchs_detail', function (Blueprint $table) {
            $table->id('dispatchs_detail_id');
            $table->foreignId('dispatch_id')->constrained('dispatchs', 'dispatch_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories', 'inventory_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('dispatched_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatchs_detail');
    }
};
