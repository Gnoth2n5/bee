<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentInvoice;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Support\Str;

class CreateDemoPayment extends Command
{
    protected $signature = 'create:demo-payment {email=test@example.com} {amount=10000}';
    protected $description = 'Tạo thanh toán demo để test auto verification';

    public function handle()
    {
        $email = $this->argument('email');
        $amount = $this->argument('amount');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User not found: {$email}");
            return 1;
        }

        $transactionId = 'DEMO_' . Str::random(10);

        // Tạo subscription
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_type' => 'premium',
            'status' => 'pending',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'amount' => $amount,
            'payment_method' => 'vietqr',
            'transaction_id' => $transactionId,
        ]);

        // Tạo payment invoice
        $invoice = PaymentInvoice::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'invoice_number' => PaymentInvoice::generateInvoiceNumber(),
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'currency' => 'VND',
            'payment_method' => 'vietqr',
            'status' => 'pending',
            'payment_gateway' => 'vietqr',
            'invoice_details' => [
                'package_name' => 'Gói Premium Demo',
                'duration' => '30 ngày',
                'features' => ['Demo feature 1', 'Demo feature 2']
            ],
            'expires_at' => now()->addHours(24),
            'notes' => "Demo payment for testing auto verification"
        ]);

        $this->info("✅ Demo payment created successfully!");
        $this->info("📋 Transaction ID: {$transactionId}");
        $this->info("💰 Amount: {$amount} VND");
        $this->info("👤 User: {$user->name} ({$user->email})");
        $this->info("📅 Created at: " . now()->format('Y-m-d H:i:s'));
        $this->info("");
        $this->info("🔍 Test auto verification:");
        $this->info("   php artisan payments:auto-verify --minutes=1");
        $this->info("");
        $this->info("🌐 Or visit: http://127.0.0.1:8000/test-auto-verify?minutes=1");

        return 0;
    }
}

