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
            $table->string('commercial_reference');
            $table->string('category');
            $table->foreignId('supplier_id')->constrained('suppliers', 'supplier_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('status')->default(1);
            $table->string('dependents');
            $table->timestamps();
        
       });
    }

 
    public function down(): void
    {
        Schema::dropIfExists('products'); 
    }
};
