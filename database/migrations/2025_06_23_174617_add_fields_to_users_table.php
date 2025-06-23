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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password');
            $table->text('bio')->nullable()->after('avatar');
            $table->json('preferences')->nullable()->after('bio');
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active')->after('preferences');
            $table->string('email_verification_token', 100)->nullable()->after('status');
            $table->string('password_reset_token', 100)->nullable()->after('email_verification_token');
            $table->timestamp('last_login_at')->nullable()->after('password_reset_token');
            $table->integer('login_count')->default(0)->after('last_login_at');
            
            // Add indexes
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            
            $table->dropColumn([
                'avatar',
                'bio',
                'preferences',
                'status',
                'email_verification_token',
                'password_reset_token',
                'last_login_at',
                'login_count'
            ]);
        });
    }
}; 