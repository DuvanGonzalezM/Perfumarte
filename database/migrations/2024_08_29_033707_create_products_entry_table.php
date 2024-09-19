<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('products_entry', function (Blueprint $table) {
            $table->id('product_entry_id');
            $table->foreignId('purchase_order_id')->constrained('purchase_orders', 'purchase_order_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products', 'product_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('quantity');
            $table->boolean('enable')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        
       });
    }

   
    public function down(): void
    {
        schema::dropIfExists('products_entry');
    }
};
