<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('product_entries', function (Blueprint $table) {
            $table->id('product_entry_id');
            $table->foreignId('purchase_order_id')->constrained('purchase_orders', 'purchase_order_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products', 'product_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('batch')->nullable();
            $table->timestamps();
       });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('product_entries');
    }
};
