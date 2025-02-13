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
        Schema::create('audit_inventory', function (Blueprint $table) {
            $table->id('id_audit_inventory');
            $table->foreignId('id_audits')->constrained('audits', 'id_audits')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories', 'inventory_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('confirmation_inventory')->nullable();
            $table->integer('quantity_system');
            $table->string('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_inventory');
    }
};
