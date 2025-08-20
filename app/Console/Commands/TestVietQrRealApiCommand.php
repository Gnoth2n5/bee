<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestVietQrRealApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:vietqr-real {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test VietQR API thực tế với key mới';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amount = $this->argument('amount') ?? 199000;

        $this->info("Testing VietQR Real API for amount: {$amount} VNĐ");

        $baseUrl = 'https://api-merchant.payos.vn';
        $endpoint = '/v2/payment-requests';
        $url = $baseUrl . $endpoint;

        $clientId = '3579f618-8849-46c7-9ab5-69cdf18dc72f';
        $apiKey = '0b0bf5e9-0d3b-4906-88a3-3cf3c826e17b';

        $orderCode = 'TEST_' . time();
        $description = "Test thanh toan - {$amount} VND";
        $signature = hash_hmac('sha256', $orderCode . $amount . $description, $apiKey);

        $payload = [
            'orderCode' => $orderCode,
            'amount' => $amount,
            'description' => $description,
            'cancelUrl' => 'https://example.com/cancel',
            'returnUrl' => 'https://example.com/success',
            'signature' => $signature,
            'items' => [
                [
                    'name' => 'Gói dịch vụ',
                    'quantity' => 1,
                    'price' => $amount
                ]
            ]
        ];

        $headers = [
            'x-client-id' => $clientId,
            'x-api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ];

        $this->info("URL: {$url}");
        $this->info("Headers: " . json_encode($headers, JSON_PRETTY_PRINT));
        $this->info("Payload: " . json_encode($payload, JSON_PRETTY_PRINT));

        try {
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post($url, $payload);

            $this->info("Response Status: " . $response->status());
            $this->info("Response Body: " . $response->body());

            if ($response->successful()) {
                $result = $response->json();
                $this->info("Response JSON: " . json_encode($result, JSON_PRETTY_PRINT));

                if (isset($result['code']) && $result['code'] === '00') {
                    $this->info("✅ API call successful!");
                    $this->info("QR Code URL: " . ($result['data']['qrDataURL'] ?? 'N/A'));
                    $this->info("QR Text: " . ($result['data']['qrData'] ?? 'N/A'));
                } else {
                    $this->error("❌ API returned error: " . ($result['message'] ?? 'Unknown error'));
                }
            } else {
                $this->error("❌ HTTP request failed with status: " . $response->status());
            }

        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
        }

        return 0;
    }
}
