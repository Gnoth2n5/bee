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
        Schema::create('dietary_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disease_condition_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Tên quy tắc (VD: Hạn chế muối, Giảm đường)
            $table->text('description')->nullable(); // Mô tả quy tắc
            $table->json('food_categories')->nullable(); // Danh mục thực phẩm liên quan
            $table->json('ingredients')->nullable(); // Nguyên liệu cụ thể
            $table->json('cooking_restrictions')->nullable(); // Hạn chế nấu nướng
            $table->json('portion_limits')->nullable(); // Giới hạn khẩu phần
            $table->json('substitutions')->nullable(); // Thay thế thực phẩm
            $table->integer('priority')->default(1); // Độ ưu tiên
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['disease_condition_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dietary_rules');
    }
};
