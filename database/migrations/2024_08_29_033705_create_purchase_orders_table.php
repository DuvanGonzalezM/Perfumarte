<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id('purchase_order_id');
            $table->String('supplier_order');
            $table->timestamps();
        
       });
    }

  
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
