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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('reference');
            $table->string('measurement_unit');
            $table->foreignId('supplier_id')->constrained('suppliers', 'supplier_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        
       });
    }

 
    public function down(): void
    {
        schema::dropIfExists('products');
    }
};
