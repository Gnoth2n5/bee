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
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->onDelete('cascade');
            $table->string('ingredient_name');
            $table->decimal('amount', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_checked')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('category')->nullable(); // Để nhóm ingredients
            $table->foreignId('recipe_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('weekly_meal_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['shopping_list_id', 'is_checked']);
            $table->index(['shopping_list_id', 'category']);
            $table->index(['ingredient_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};
