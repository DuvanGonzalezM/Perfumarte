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
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id('cash_register_id');
            $table->foreignId('location_id')->constrained('locations', 'location_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('total_collected');
            $table->integer('count_100_bill');
            $table->integer('count_50_bill');
            $table->integer('count_20_bill');
            $table->integer('count_10_bill');
            $table->integer('count_5_bill');
            $table->integer('count_2_bill');
            $table->integer('count_1_bill');
            $table->integer('total_coins');
            $table->text('observations')->nullable();
            $table->boolean('confirmationclosingcash')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
