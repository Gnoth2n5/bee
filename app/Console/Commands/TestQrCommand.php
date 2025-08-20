<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietqrInformation;

class TestQrCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:qr {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test QR code generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = $this->argument('amount') ?? 199000;

        $this->info("Testing QR code generation for amount: {$amount} VNĐ");

        // Lấy tài khoản VietQR
        $vietqrAccount = VietqrInformation::where('status', 1)->first();

        if (!$vietqrAccount) {
            $this->error("Không tìm thấy tài khoản VietQR!");
            return 1;
        }

        $this->line("VietQR Account: {$vietqrAccount->name}");
        $this->line("Account: {$vietqrAccount->account}");

        try {
            // Tạo QR code
            $qrData = $vietqrAccount->generatePaymentCodeFromArray([
                'transaction_amount' => $amount,
                'message' => "Test thanh toan - {$amount} VND",
                'transaction_id' => 'TEST_' . time(),
            ]);

            $this->info("QR Code generated successfully!");
            $this->line("QR Data: " . json_encode($qrData, JSON_PRETTY_PRINT));

            return 0;
        } catch (\Exception $e) {
            $this->error("Error generating QR code: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
