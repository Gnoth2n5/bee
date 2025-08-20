<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use App\Models\PaymentInvoice;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tìm user có ID = 7 hoặc tạo user mới
        $user = User::find(7);
        if (!$user) {
            $user = User::first();
        }

        if ($user) {
            // Tạo subscription VIP cho user
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_type' => 'vip',
                'status' => 'active',
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'amount' => 199000,
                'payment_method' => 'vietqr',
                'transaction_id' => 'TEST_' . time(),
                'payment_details' => json_encode([
                    'method' => 'vietqr',
                    'status' => 'completed',
                    'completed_at' => Carbon::now()->subDays(5)
                ])
            ]);

            echo "Created VIP subscription for user: {$user->name}\n";

            // Tạo dữ liệu test cho thanh toán
            PaymentInvoice::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'invoice_number' => 'INV-001',
                'transaction_id' => 'TXN_TEST_001',
                'amount' => 1000,
                'currency' => 'VND',
                'payment_method' => 'vietqr',
                'status' => 'paid',
                'payment_gateway' => 'vietqr',
                'paid_at' => now(),
                'payment_details' => json_encode([
                    'method' => 'vietqr',
                    'verified_at' => now()
                ])
            ]);
        } else {
            echo "No user found to create subscription for\n";
        }
    }
}
