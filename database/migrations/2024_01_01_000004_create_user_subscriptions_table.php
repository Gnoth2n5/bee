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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subscription_type'); // vip, premium, basic
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable(); // vietqr, stripe, etc.
            $table->string('transaction_id')->nullable();
            $table->text('payment_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
