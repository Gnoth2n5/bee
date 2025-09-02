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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('summary', 500)->nullable();
            
            // Cooking details
            $table->integer('cooking_time')->nullable(); // in minutes
            $table->integer('preparation_time')->nullable(); // in minutes
            $table->integer('total_time')->nullable(); // calculated field
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('servings')->default(1);
            $table->integer('calories_per_serving')->nullable();
            
            // Content
            $table->json('ingredients'); // Structured ingredients data
            $table->json('instructions'); // Step-by-step instructions
            $table->text('tips')->nullable();
            $table->text('notes')->nullable();
            
            // Media
            $table->string('featured_image')->nullable();
            $table->string('video_url', 500)->nullable();
            
            // Status & workflow
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // SEO & metadata
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();
            
            // Statistics
            $table->integer('view_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->integer('rating_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index('user_id');
            $table->index('status');
            $table->index('approved_by');
            $table->index('published_at');
            $table->index('created_at');
            $table->index('average_rating');
            $table->index('view_count');
            
            // Full-text search index
            $table->fullText(['title', 'description', 'summary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
