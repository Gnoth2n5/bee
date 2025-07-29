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
        Schema::create('moderation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên quy tắc');
            $table->text('keywords')->comment('Danh sách từ khóa cấm, phân cách bằng dấu phẩy');
            $table->enum('action', ['reject', 'flag', 'auto_approve'])->default('reject')->comment('Hành động khi vi phạm');
            $table->text('description')->nullable()->comment('Mô tả quy tắc');
            $table->boolean('is_active')->default(true)->comment('Trạng thái hoạt động');
            $table->integer('priority')->default(1)->comment('Độ ưu tiên (số càng cao càng ưu tiên)');
            $table->text('fields_to_check')->comment('Các trường cần kiểm tra');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('Người tạo quy tắc');
            $table->timestamps();

            $table->index('is_active');
            $table->index('priority');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_rules');
    }
};