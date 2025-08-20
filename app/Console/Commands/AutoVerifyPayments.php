<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentInvoice;
use App\Models\UserSubscription;
use App\Services\VietQrService;
use Illuminate\Support\Facades\Log;

class AutoVerifyPayments extends Command
{
    protected $signature = 'payments:auto-verify {--minutes=5}';
    protected $description = 'Tự động kiểm tra và xác nhận thanh toán qua PayOS API';

    public function handle()
    {
        $minutes = $this->option('minutes');

        $this->info("🔍 Đang kiểm tra thanh toán chờ xác nhận (sau {$minutes} phút)...");

        // Lấy danh sách thanh toán pending đã tạo từ X phút trước
        $pendingPayments = PaymentInvoice::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes($minutes))
            ->with(['user', 'subscription'])
            ->get();

        if ($pendingPayments->isEmpty()) {
            $this->info("✅ Không có thanh toán nào cần kiểm tra.");
            return 0;
        }

        $this->info("📋 Tìm thấy {$pendingPayments->count()} thanh toán cần kiểm tra...");

        $vietqrService = new VietQrService();
        $successCount = 0;
        $failedCount = 0;

        foreach ($pendingPayments as $payment) {
            $this->info("🔍 Kiểm tra thanh toán: {$payment->transaction_id} - {$payment->user->email}");

            try {
                // Kiểm tra trạng thái thanh toán qua PayOS API
                $paymentStatus = $vietqrService->checkPaymentStatus($payment->transaction_id);

                if (
                    $paymentStatus['success'] &&
                    ($paymentStatus['status'] === 'success' || $paymentStatus['status'] === 'completed') &&
                    $paymentStatus['amount'] == $payment->amount
                ) {

                    // Thanh toán thành công - tự động xác nhận
                    $this->autoApprovePayment($payment, $paymentStatus);
                    $successCount++;
                    $this->info("✅ Tự động xác nhận thành công: {$payment->transaction_id}");

                } else {
                    $failedCount++;
                    $this->warn("❌ Thanh toán chưa hoàn tất: {$payment->transaction_id}");
                }

            } catch (\Exception $e) {
                $failedCount++;
                $this->error("💥 Lỗi kiểm tra thanh toán {$payment->transaction_id}: " . $e->getMessage());
                Log::error("Auto verify payment error", [
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n📊 Kết quả:");
        $this->info("✅ Tự động xác nhận: {$successCount}");
        $this->info("❌ Chưa hoàn tất: {$failedCount}");

        return 0;
    }

    private function autoApprovePayment($payment, $paymentStatus)
    {
        try {
            // Cập nhật trạng thái hóa đơn
            $payment->markAsPaid([
                'verified_at' => now(),
                'method' => 'auto_verification',
                'api_response' => $paymentStatus,
                'notes' => 'Tự động xác nhận qua PayOS API'
            ]);

            // Kích hoạt gói dịch vụ
            if ($payment->subscription) {
                $payment->subscription->update([
                    'status' => 'active',
                    'payment_details' => json_encode([
                        'verified_at' => now(),
                        'method' => 'auto_verification',
                        'api_response' => $paymentStatus,
                        'notes' => 'Tự động xác nhận qua PayOS API'
                    ])
                ]);
            }

            // Gửi email thông báo (nếu có)
            try {
                if ($payment->user && $payment->user->email) {
                    // Mail::to($payment->user->email)->send(new PaymentSuccessMail($payment));
                    Log::info("Payment auto-approved email sent", [
                        'user_id' => $payment->user->id,
                        'email' => $payment->user->email,
                        'transaction_id' => $payment->transaction_id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to send auto-approval email", [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info("Payment auto-approved successfully", [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'user_id' => $payment->user->id,
                'amount' => $payment->amount
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to auto-approve payment", [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
