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
        Schema::create('weather_recipe_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('city_code', 20)->collation('utf8mb4_unicode_ci'); // Mã tỉnh/thành phố
            $table->string('weather_condition')->collation('utf8mb4_unicode_ci'); // Điều kiện thời tiết
            $table->decimal('temperature_min', 5, 2); // Nhiệt độ tối thiểu
            $table->decimal('temperature_max', 5, 2); // Nhiệt độ tối đa
            $table->integer('humidity_min')->nullable(); // Độ ẩm tối thiểu
            $table->integer('humidity_max')->nullable(); // Độ ẩm tối đa
            $table->json('recipe_ids'); // Danh sách ID công thức phù hợp
            $table->json('categories'); // Danh mục món ăn phù hợp
            $table->text('suggestion_reason')->collation('utf8mb4_unicode_ci'); // Lý do đề xuất
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1); // Độ ưu tiên
            $table->timestamp('last_generated');
            $table->timestamps();
            
            $table->index(['city_code', 'weather_condition', 'is_active'], 'weather_suggestions_city_weather_active_idx');
            $table->index('last_generated', 'weather_suggestions_last_generated_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_recipe_suggestions');
    }
};