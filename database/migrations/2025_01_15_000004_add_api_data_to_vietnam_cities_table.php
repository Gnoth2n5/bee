<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vietnam_cities', function (Blueprint $table) {
            $table->json('api_data')->nullable()->after('longitude');
            $table->string('codename')->nullable()->after('code');
        });
    }

    public function down(): void
    {
        Schema::table('vietnam_cities', function (Blueprint $table) {
            $table->dropColumn(['api_data', 'codename']);
        });
    }
}; 