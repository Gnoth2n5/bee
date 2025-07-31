<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm giá trị 'pending' vào enum status
        DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'archived') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa giá trị 'pending' khỏi enum status
        DB::statement("ALTER TABLE posts MODIFY COLUMN status ENUM('draft', 'published', 'archived') DEFAULT 'draft'");
    }
};
