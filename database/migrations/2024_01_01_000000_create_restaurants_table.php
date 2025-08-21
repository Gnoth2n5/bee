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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('place_id')->unique();
            $table->string('name');
            $table->text('formatted_address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('rating', 2, 1)->nullable();
            $table->integer('user_ratings_total')->default(0);
            $table->string('formatted_phone_number')->nullable();
            $table->string('website')->nullable();
            $table->integer('price_level')->nullable();
            $table->json('types')->nullable();
            $table->json('opening_hours')->nullable();
            $table->json('photos')->nullable();
            $table->json('reviews')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('rating');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
