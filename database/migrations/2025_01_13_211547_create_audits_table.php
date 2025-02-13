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
        Schema::create('audits', function (Blueprint $table) {
            $table->id('id_audits');
            $table->foreignId('user_id')->constrained('users', 'user_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations', 'location_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('type_audit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
