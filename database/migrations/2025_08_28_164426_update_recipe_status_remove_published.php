<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật tất cả recipe có status = 'published' thành 'approved'
        DB::table('recipes')
            ->where('status', 'published')
            ->update([
                    'status' => 'approved',
                    'updated_at' => now()
                ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần reverse vì đã bỏ status 'published'
    }
};
