<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PaymentService
{
    /**
     * VIP package prices (simple single payment like old project)
     */
    const VIP_PRICES = [
        'vip' => 2000,  // 2,000 VND for VIP access
    ];

    /**
     * Create payment from bank transfer webhook (like old project)
     */
    public function createFromWebhook(array $payload): Payment
    {
        return DB::transaction(function () use ($payload) {
            $transferAmount = (int) data_get($payload, 'transferAmount', 0);

            // Validate amount (simple 2000 VND like old project)
            if ($transferAmount != self::VIP_PRICES['vip']) {
                throw new \Exception('Số tiền không hợp lệ, vui lòng thanh toán 2.000đ');
            }

            // Extract user ID from description (format: "DH123" like old project)
            $description = data_get($payload, 'description', '');
            if (!preg_match('/DH(\d+)/', $description, $matches)) {
                throw new \Exception('Mô tả thanh toán không hợp lệ. Vui lòng sử dụng mã "DH[ID]"');
            }

            $userId = $matches[1];
            $user = User::find($userId);

            if (!$user) {
                throw new \Exception('Không tìm thấy tài khoản');
            }

            $payment = Payment::create([
                'user_id' => $user->id,
                'gateway' => data_get($payload, 'gateway'),
                'transaction_date' => data_get($payload, 'transactionDate'),
                'account_number' => data_get($payload, 'accountNumber'),
                'code' => data_get($payload, 'code'),
                'content' => data_get($payload, 'content'),
                'transfer_type' => data_get($payload, 'transferType'),
                'transfer_amount' => $transferAmount,
                'accumulated' => data_get($payload, 'accumulated'),
                'sub_account' => data_get($payload, 'subAccount'),
                'reference_code' => data_get($payload, 'referenceCode'),
                'description' => $description,
                'status' => 'completed',
                'raw_payload' => $payload,
            ]);

            // Upgrade user to VIP (simple upgrade like old project)
            if ($payment->user && $transferAmount == self::VIP_PRICES['vip']) {
                $payment->user->profile()->updateOrCreate(
                    ['user_id' => $payment->user->id],
                    ['isVipAccount' => true]
                );

                // Store payment success notification in cache for 10 minutes
                Cache::put("vip_payment_success_{$payment->user->id}", [
                    'user_id' => $payment->user->id,
                    'amount' => $transferAmount,
                    'payment_id' => $payment->id,
                    'message' => 'Thanh toán VIP thành công! Tài khoản của bạn đã được nâng cấp.',
                    'timestamp' => now()->toISOString()
                ], 600); // 10 minutes
            }

            Log::info('VIP payment processed successfully', [
                'user_id' => $user->id,
                'amount' => $transferAmount,
                'payment_id' => $payment->id
            ]);

            return $payment;
        });
    }

    /**
     * Get VIP pricing information (simple like old project)
     */
    public function getVipPricing(): array
    {
        return [
            'vip' => [
                'price' => self::VIP_PRICES['vip'],
                'duration' => 'Vĩnh viễn',
                'savings' => 0
            ]
        ];
    }

    /**
     * Generate payment data for user (simple like old project)
     */
    public function generatePaymentData(User $user): array
    {
        $amount = self::VIP_PRICES['vip'];
        $description = "DH{$user->id}";

        return [
            'amount' => $amount,
            'description' => $description,
            'user_id' => $user->id,
            'message' => "Chuyển khoản {$amount}đ với nội dung: {$description}"
        ];
    }
}
