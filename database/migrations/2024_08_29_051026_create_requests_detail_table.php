<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('requests_detail', function (Blueprint $table) {
            $table->id('request_detail_id');
            $table->foreignId('request_id')->constrained('requests', 'request_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories', 'inventory_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('quantity')->nullable();
            $table->timestamps();
        
       });
    }

  
    public function down(): void
    {
        schema::dropIfExists('requests_detail');
    }
};
