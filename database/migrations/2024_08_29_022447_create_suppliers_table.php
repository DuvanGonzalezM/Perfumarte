<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('supplier_id');
            $table->string('nit');
            $table->string('name');
            $table->string('country');
            $table->string('address');
            $table->string('phone');
            $table->string('email');
            $table->boolean('enable')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
    }



    public function down(): void
    {
        schema::dropIfExists('suppliers');
    }
};
