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
        Schema::create('locations', function (Blueprint $table) {
            $table->id('location_id');
            $table->string('name');
            $table->string('address');
            $table->foreignId('zone_id')->nullable()->constrained('zones', 'zone_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('cash_base');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
