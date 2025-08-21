<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentInvoice;
use App\Models\UserSubscription;
use App\Models\User;
use Illuminate\Support\Str;

class TestAdminPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:admin-payment {amount=10000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo thanh toán test để admin kiểm duyệt';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = $this->argument('amount');

        $this->info("Tạo thanh toán test với số tiền: {$amount} VND");

        // Tạo user test nếu chưa có
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now()
            ]
        );

        $this->info("Sử dụng user: {$user->name} ({$user->email})");

        // Tạo transaction ID
        $transactionId = 'TXN_' . Str::random(10);

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

        // Tạo hóa đơn thanh toán
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
                'package_name' => 'Gói Premium',
                'duration' => '30 ngày',
                'features' => [
                    'Tất cả tính năng cơ bản',
                    'Tìm món ăn theo bản đồ nâng cao',
                    'Quảng cáo cửa hàng của bạn',
                    'Ưu tiên hiển thị trong tìm kiếm',
                    'Thống kê chi tiết'
                ]
            ],
            'expires_at' => now()->addHours(24),
            'notes' => "Thanh toán test gói premium cho {$user->name}"
        ]);

        $this->info("✅ Thanh toán test đã được tạo!");
        $this->info("📋 Thông tin thanh toán:");
        $this->info("   - Mã GD: {$transactionId}");
        $this->info("   - Số tiền: {$amount} VND");
        $this->info("   - User: {$user->name}");
        $this->info("   - Trạng thái: Pending (chờ kiểm duyệt)");
        $this->info("");
        $this->info("🔗 Admin có thể kiểm duyệt tại:");
        $this->info("   http://127.0.0.1:8000/admin/payments");
        $this->info("");
        $this->info("📝 Để phê duyệt, admin cần:");
        $this->info("   1. Truy cập trang admin payments");
        $this->info("   2. Tìm thanh toán với mã: {$transactionId}");
        $this->info("   3. Nhấn 'Phê duyệt'");
        $this->info("   4. Gói Premium sẽ được kích hoạt cho user");

        return 0;
    }
}
