<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->integer('request_type');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status'); 
            $table->boolean('enable')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        
       });
    }

   
    public function down(): void
    {
        schema::dropIfExists('requests');
    }
};
