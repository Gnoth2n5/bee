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
        Schema::create('recipe_disease_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->foreignId('disease_condition_id')->constrained()->onDelete('cascade');
            $table->enum('suitability', ['suitable', 'moderate', 'unsuitable'])->default('moderate');
            $table->text('notes')->nullable(); // Ghi chú về tính phù hợp
            $table->json('modifications')->nullable(); // Các điều chỉnh cần thiết
            $table->timestamps();

            $table->unique(['recipe_id', 'disease_condition_id']);
            $table->index(['disease_condition_id', 'suitability']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_disease_conditions');
    }
};
