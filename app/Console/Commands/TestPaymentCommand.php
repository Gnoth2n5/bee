<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PaymentInvoice;
use App\Models\UserSubscription;
use App\Services\VietQrService;
use App\Mail\PaymentSuccessMail;
use Illuminate\Support\Facades\Mail;

class TestPaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:payment {user_id?} {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test toàn bộ hệ thống thanh toán';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id') ?? 7;
        $amount = $this->argument('amount') ?? 199000;

        $this->info("Testing payment system for user ID: {$userId}, amount: {$amount} VNĐ");

        // 1. Kiểm tra user
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        $this->info("User: {$user->name} ({$user->email})");

        // 2. Test VietQR Service
        $this->info("\n1. Testing VietQR Service...");
        $vietqrService = new VietQrService();

        $qrData = $vietqrService->generateQrCode([
            'account_no' => '0123456789',
            'account_name' => 'Bee Food Demo',
            'bank_code' => '970436',
            'amount' => $amount,
            'message' => "Test thanh toan - {$amount} VND",
            'transaction_id' => 'TEST_' . time(),
        ]);

        if ($qrData['success']) {
            $this->info("✅ QR Code generated successfully!");
            $this->line("Message: " . $qrData['message']);
            if (isset($qrData['is_demo'])) {
                $this->warn("⚠️ Using demo mode (API not available)");
            }
        } else {
            $this->error("❌ Failed to generate QR code: " . $qrData['message']);
            return 1;
        }

        // 3. Test tạo subscription và invoice
        $this->info("\n2. Testing subscription and invoice creation...");

        $transactionId = 'TEST_' . time();

        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_type' => 'vip',
            'status' => 'pending',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'amount' => $amount,
            'payment_method' => 'vietqr',
            'transaction_id' => $transactionId,
        ]);

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
                'package_name' => 'Gói VIP',
                'duration' => '30 ngày',
                'features' => ['Tính năng VIP 1', 'Tính năng VIP 2']
            ],
            'expires_at' => now()->addHours(24),
            'notes' => "Test thanh toán gói VIP cho {$user->name}"
        ]);

        $this->info("✅ Subscription and invoice created!");
        $this->line("Subscription ID: {$subscription->id}");
        $this->line("Invoice Number: {$invoice->invoice_number}");

        // 4. Test kích hoạt thanh toán
        $this->info("\n3. Testing payment activation...");

        $invoice->markAsPaid([
            'verified_at' => now(),
            'method' => 'test_verification',
            'test_mode' => true
        ]);

        $subscription->update([
            'status' => 'active',
            'payment_details' => json_encode([
                'verified_at' => now(),
                'method' => 'test_verification',
                'test_mode' => true
            ])
        ]);

        $this->info("✅ Payment activated successfully!");

        // 5. Test gửi email
        $this->info("\n4. Testing email sending...");

        try {
            Mail::to($user->email)->send(new PaymentSuccessMail($invoice));
            $this->info("✅ Email sent successfully!");
            $this->line("Check logs for email content (using log driver)");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
        }

        // 6. Test VIP status
        $this->info("\n5. Testing VIP status...");

        $user->refresh();
        $isVip = $user->isVip();
        $activeSubscription = $user->activeSubscription();

        $this->info("Is VIP: " . ($isVip ? 'Yes' : 'No'));
        if ($activeSubscription) {
            $this->line("Active subscription: {$activeSubscription->subscription_type}");
            $this->line("End date: {$activeSubscription->end_date->format('d/m/Y')}");
        }

        // 7. Cleanup test data
        $this->info("\n6. Cleaning up test data...");

        $subscription->delete();
        $invoice->delete();

        $this->info("✅ Test data cleaned up!");

        $this->info("\n🎉 Payment system test completed successfully!");
        return 0;
    }
}
