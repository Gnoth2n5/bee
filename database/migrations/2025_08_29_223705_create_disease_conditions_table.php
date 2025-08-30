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
        Schema::create('disease_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên bệnh (VD: Tiểu đường, Cao huyết áp, Gout)
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // Mô tả bệnh
            $table->json('symptoms')->nullable(); // Các triệu chứng
            $table->json('restricted_foods')->nullable(); // Thực phẩm cần tránh
            $table->json('recommended_foods')->nullable(); // Thực phẩm nên ăn
            $table->json('nutritional_requirements')->nullable(); // Yêu cầu dinh dưỡng
            $table->json('cooking_methods')->nullable(); // Phương pháp nấu phù hợp
            $table->json('meal_timing')->nullable(); // Thời gian ăn phù hợp
            $table->integer('severity_level')->default(1); // Mức độ nghiêm trọng (1-5)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['slug', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disease_conditions');
    }
};
