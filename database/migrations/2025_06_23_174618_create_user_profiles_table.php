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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Vietnam');
            $table->string('timezone', 50)->default('Asia/Ho_Chi_Minh');
            $table->string('language', 10)->default('vi');
            $table->json('dietary_preferences')->nullable(); // Vegan, gluten-free, etc.
            $table->json('allergies')->nullable(); // Food allergies
            $table->json('health_conditions')->nullable(); // Medical conditions
            $table->enum('cooking_experience', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};