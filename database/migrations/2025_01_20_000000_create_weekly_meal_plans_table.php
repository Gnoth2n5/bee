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
        Schema::create('weekly_meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('week_start');
            $table->json('meals')->nullable(); // Store meal plan structure
            $table->boolean('is_active')->default(true);
            $table->integer('total_calories')->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->boolean('shopping_list_generated')->default(false);
            $table->boolean('weather_optimized')->default(false);
            $table->boolean('ai_suggestions_used')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'week_start']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_meal_plans');
    }
};
