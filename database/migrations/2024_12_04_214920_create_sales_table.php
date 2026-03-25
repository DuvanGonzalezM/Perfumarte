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
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sale_id');
            $table->foreignId('cash_register_id')->constrained('cash_registers', 'cash_register_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('total');
            $table->string('payment_method');
            $table->string('transaction_code');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            // Índice en created_at: se filtra por fecha en la vista de ventas del día
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
