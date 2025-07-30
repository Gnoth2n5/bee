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
        Schema::create('vietnam_cities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên tỉnh/thành phố
            $table->string('code', 20)->unique(); // Mã tỉnh/thành phố
            $table->string('region'); // Vùng miền (Bắc, Trung, Nam)
            $table->decimal('latitude', 10, 8); // Vĩ độ
            $table->decimal('longitude', 11, 8); // Kinh độ
            $table->string('timezone')->default('Asia/Ho_Chi_Minh');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['region', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vietnam_cities');
    }
};