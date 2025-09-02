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
        Schema::table('recipes', function (Blueprint $table) {
            $table->json('embedding')->nullable()->after('meta_keywords');
            $table->timestamp('embedding_generated_at')->nullable()->after('embedding');
            $table->index(['status', 'embedding_generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['status', 'embedding_generated_at']);
            $table->dropColumn(['embedding', 'embedding_generated_at']);
        });
    }
};
