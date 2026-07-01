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
        Schema::create('equipment_integration_system', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('integration_system_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('compatible_model')->nullable();

            $table->timestamps();

            $table->unique(['equipment_id', 'integration_system_id'], 'equipment_system_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_integration_system');
    }
};
