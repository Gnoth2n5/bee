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
        Schema::table('user_profiles', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('user_profiles', 'isVipAccount')) {
                $table->boolean('isVipAccount')->default(false)->after('cooking_experience');
            }
            if (!Schema::hasColumn('user_profiles', 'vip_expires_at')) {
                $table->timestamp('vip_expires_at')->nullable()->after('isVipAccount');
            }
            if (!Schema::hasColumn('user_profiles', 'vip_plan')) {
                $table->string('vip_plan')->nullable()->after('vip_expires_at'); // monthly, yearly
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['isVipAccount', 'vip_expires_at', 'vip_plan']);
        });
    }
};
