<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_cash', function (Blueprint $table) {
            $table->id('audit_cash_id');
            $table->foreignId('id_audits')->constrained('audits', 'id_audits')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('money_in_box');
            $table->integer('meny_in_digital');
            $table->boolean('confirmation_cash');
            $table->boolean('confirmation_digital');
            $table->string('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_cash');
    }
};
