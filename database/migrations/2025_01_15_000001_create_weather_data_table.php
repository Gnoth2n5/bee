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
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->string('city_name');
            $table->string('city_code', 20); // Mã tỉnh/thành phố
            $table->decimal('temperature', 5, 2); // Nhiệt độ (Celsius)
            $table->decimal('feels_like', 5, 2); // Nhiệt độ cảm nhận
            $table->integer('humidity'); // Độ ẩm (%)
            $table->decimal('wind_speed', 5, 2); // Tốc độ gió (m/s)
            $table->string('weather_condition'); // Điều kiện thời tiết (sunny, rainy, etc.)
            $table->string('weather_description'); // Mô tả thời tiết
            $table->string('weather_icon'); // Icon thời tiết
            $table->integer('pressure'); // Áp suất (hPa)
            $table->integer('visibility'); // Tầm nhìn (m)
            $table->decimal('uv_index', 3, 1)->nullable(); // Chỉ số UV
            $table->json('forecast_data')->nullable(); // Dữ liệu dự báo 5 ngày
            $table->timestamp('last_updated');
            $table->timestamps();

            $table->index(['city_code', 'last_updated']);
            $table->unique(['city_code', 'last_updated']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};