<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VietQrService;
use App\Models\VietqrInformation;

class TestVietQrApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:vietqr-api {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test VietQR API integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = $this->argument('amount') ?? 199000;
        
        $this->info("Testing VietQR API integration for amount: {$amount} VNĐ");
        
        // Lấy tài khoản VietQR
        $vietqrAccount = VietqrInformation::with('bank')->where('status', 1)->first();
        
        if (!$vietqrAccount) {
            $this->error("Không tìm thấy tài khoản VietQR!");
            return 1;
        }
        
        $this->line("VietQR Account: {$vietqrAccount->name}");
        $this->line("Account: {$vietqrAccount->account}");
        $this->line("Bank: {$vietqrAccount->bank->name} ({$vietqrAccount->bank->code})");
        
        $vietqrService = new VietQrService();
        
        // Test tạo QR code
        $this->info("\n1. Testing QR Code Generation...");
        
        $qrData = $vietqrService->generateQrCode([
            'account_no' => $vietqrAccount->account,
            'account_name' => $vietqrAccount->name,
            'bank_code' => $vietqrAccount->bank->code,
            'amount' => $amount,
            'message' => "Test thanh toan - {$amount} VND",
            'transaction_id' => 'TEST_' . time(),
        ]);
        
        if ($qrData['success']) {
            $this->info("✅ QR Code generated successfully!");
            $this->line("QR Code URL: " . ($qrData['qr_code'] ?? 'N/A'));
            $this->line("QR Text: " . ($qrData['qr_text'] ?? 'N/A'));
            $this->line("Transaction ID: " . ($qrData['transaction_id'] ?? 'N/A'));
        } else {
            $this->error("❌ Failed to generate QR code: " . $qrData['message']);
            return 1;
        }
        
        // Test kiểm tra trạng thái (sẽ fail vì transaction_id không tồn tại)
        $this->info("\n2. Testing Payment Status Check...");
        
        $statusData = $vietqrService->checkPaymentStatus($qrData['transaction_id']);
        
        if ($statusData['success']) {
            $this->info("✅ Payment status checked successfully!");
            $this->line("Status: " . $statusData['status']);
            $this->line("Amount: " . $statusData['amount']);
            $this->line("Message: " . $statusData['message']);
        } else {
            $this->warn("⚠️ Payment status check failed (expected for test transaction): " . $statusData['message']);
        }
        
        // Test danh sách ngân hàng
        $this->info("\n3. Testing Bank List...");
        
        $banks = $vietqrService->getBanks();
        $this->info("✅ Found " . count($banks) . " banks in configuration");
        
        foreach (array_slice($banks, 0, 5) as $code => $bin) {
            $this->line("- {$code}: {$bin}");
        }
        
        $this->info("\n🎉 VietQR API integration test completed!");
        return 0;
    }
}
