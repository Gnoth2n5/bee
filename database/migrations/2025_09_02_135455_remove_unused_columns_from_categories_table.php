<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Check and drop foreign key constraint if exists
            if (Schema::hasColumn('categories', 'created_by')) {
                $table->dropForeign(['created_by']);
            }

            // Drop unused columns one by one
            if (Schema::hasColumn('categories', 'color')) {
                $table->dropColumn('color');
            }

            if (Schema::hasColumn('categories', 'level')) {
                $table->dropColumn('level');
            }

            if (Schema::hasColumn('categories', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Add back the columns
            $table->string('color', 7)->nullable()->after('icon');
            $table->integer('level')->default(0)->after('parent_id');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('is_active');

            // Add back the index
            $table->index('level');
        });
    }
};
