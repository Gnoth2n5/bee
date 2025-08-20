<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VietQrService;

class TestQrGenerationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:qr-gen {amount?}';

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

        $this->info("Testing QR Code Generation for amount: {$amount} VNĐ");

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
            $this->line("QR Code length: " . strlen($qrData['qr_code']));
            $this->line("QR Code starts with: " . substr($qrData['qr_code'], 0, 50) . '...');

            if (isset($qrData['is_demo'])) {
                $this->warn("⚠️ Using demo mode");
            }

            // Debug: Kiểm tra xem có phải fallback mode không
            if (strpos($qrData['message'], 'fallback') !== false) {
                $this->error("❌ Using fallback mode - QrCode library may have issues");
            }

            // Lưu QR code vào file để test
            $filename = 'test_qr_' . time() . '.html';
            $html = "
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test QR Code</title>
            </head>
            <body>
                <h1>Test QR Code</h1>
                <p>Amount: " . number_format($amount) . " VND</p>
                <p>Transaction ID: " . $qrData['transaction_id'] . "</p>
                <p>Message: " . $qrData['message'] . "</p>
                <div>
                    <img src='" . $qrData['qr_code'] . "' alt='QR Code' style='width: 300px; height: 300px; border: 1px solid #ccc;'>
                </div>
                <h3>QR Text:</h3>
                <pre>" . htmlspecialchars($qrData['qr_text']) . "</pre>
            </body>
            </html>
            ";

            file_put_contents(public_path($filename), $html);
            $this->info("QR Code saved to: " . public_path($filename));
            $this->info("View at: http://127.0.0.1:8000/{$filename}");

        } else {
            $this->error("❌ Failed to generate QR code: " . $qrData['message']);
            return 1;
        }

        return 0;
    }
}
