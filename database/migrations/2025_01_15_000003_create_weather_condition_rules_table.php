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
        Schema::create('weather_condition_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên quy tắc (VD: "Nhiệt độ cao độ ẩm cao")
            $table->text('description')->nullable(); // Mô tả quy tắc
            
            // Điều kiện nhiệt độ
            $table->decimal('temperature_min', 5, 2)->nullable(); // Nhiệt độ tối thiểu
            $table->decimal('temperature_max', 5, 2)->nullable(); // Nhiệt độ tối đa
            
            // Điều kiện độ ẩm
            $table->integer('humidity_min')->nullable(); // Độ ẩm tối thiểu
            $table->integer('humidity_max')->nullable(); // Độ ẩm tối đa
            
            // Categories được đề xuất
            $table->json('suggested_categories')->nullable(); // ID các category phù hợp
            
            // Tags được đề xuất
            $table->json('suggested_tags')->nullable(); // ID các tag phù hợp
            
            // Lý do đề xuất
            $table->text('suggestion_reason');
            
            // Trạng thái và ưu tiên
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1); // Độ ưu tiên (cao hơn = ưu tiên hơn)
            
            // Thời gian áp dụng (có thể theo mùa)
            $table->json('seasonal_rules')->nullable(); // Quy tắc theo mùa
            
            $table->timestamps();
            
            // Indexes
            $table->index(['temperature_min', 'temperature_max'], 'weather_rules_temp_idx');
            $table->index(['humidity_min', 'humidity_max'], 'weather_rules_humidity_idx');
            $table->index(['is_active', 'priority'], 'weather_rules_active_priority_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_condition_rules');
    }
}; 