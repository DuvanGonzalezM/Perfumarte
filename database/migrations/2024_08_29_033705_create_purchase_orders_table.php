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
            $table->boolean('enable')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        
       });
    }

  
    public function down(): void
    {
        schema::dropIfExists('purchase_orders');
    }
};
