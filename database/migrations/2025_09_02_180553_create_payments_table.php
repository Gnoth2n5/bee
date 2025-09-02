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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway')->nullable(); // VietQR, Bank Transfer, etc.
            $table->timestamp('transaction_date')->nullable();
            $table->string('account_number')->nullable();
            $table->string('code')->nullable();
            $table->text('content')->nullable();
            $table->string('transfer_type')->nullable();
            $table->integer('transfer_amount')->default(0);
            $table->integer('accumulated')->nullable();
            $table->string('sub_account')->nullable();
            $table->string('reference_code')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
