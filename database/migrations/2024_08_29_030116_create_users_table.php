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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username');
            $table->string('name');
            $table->string('boss_user')->nullable();
            $table->string('password');
            $table->boolean('default_password')->default(true);
            $table->boolean('enabled');
            $table->string('zone_id')->nullable()->constrained('zones', 'zone_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations', 'location_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
