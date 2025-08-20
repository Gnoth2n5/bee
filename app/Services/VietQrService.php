<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VietQrService
{
    private $clientId;
    private $apiKey;
    private $checksumKey;
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->clientId = config('vietqr.api.client_id');
        $this->apiKey = config('vietqr.api.api_key');
        $this->checksumKey = config('vietqr.api.checksum_key');
        $this->baseUrl = config('vietqr.api.base_url');
        $this->timeout = config('vietqr.api.timeout', 30);
    }

    /**
     * Tạo QR code thanh toán qua PayOS API
     */
    public function generateQrCode($data)
    {
        // Sử dụng demo mode nếu được cấu hình
        if (config('vietqr.api.use_demo_mode', false)) {
            Log::info('Using demo mode for QR code generation');
            return $this->generateDemoQrCode($data);
        }

        try {
            $endpoint = config('vietqr.api.generate_qr_endpoint');
            $url = $this->baseUrl . $endpoint;

            $payload = [
                'orderCode' => $data['transaction_id'] ?? 'ORDER_' . time(),
                'amount' => $data['amount'],
                'description' => $data['message'] ?? 'Thanh toán gói dịch vụ',
                'cancelUrl' => url('/payment/cancel'),
                'returnUrl' => url('/payment/success'),
                'signature' => $this->generatePayOSSignature($data),
                'items' => [
                    [
                        'name' => 'Gói dịch vụ',
                        'quantity' => 1,
                        'price' => $data['amount']
                    ]
                ],
                'webhookUrl' => config('vietqr.api.webhook_url'),
                'paymentMethodId' => 1, // VietQR
            ];

            $headers = [
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            Log::info('PayOS API Request', [
                'url' => $url,
                'payload' => $payload,
                'headers' => array_merge($headers, ['x-api-key' => '***HIDDEN***']),
                'client_id' => $this->clientId
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->post($url, $payload);

            Log::info('PayOS API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['code']) && $result['code'] === '00') {
                    Log::info('PayOS QR code generated successfully', [
                        'transaction_id' => $result['data']['orderCode'] ?? $data['transaction_id'],
                        'amount' => $data['amount']
                    ]);

                    return [
                        'success' => true,
                        'qr_code' => $result['data']['qrCode'] ?? null,
                        'qr_text' => $result['data']['qrCode'] ?? null,
                        'transaction_id' => $result['data']['orderCode'] ?? $data['transaction_id'],
                        'amount' => $data['amount'],
                        'message' => 'QR code tạo thành công',
                        'payment_url' => $result['data']['checkoutUrl'] ?? null,
                        'is_demo' => false,
                        'payos_info' => [
                            'bank' => 'Ngân hàng TMCP Quân đội',
                            'account_name' => 'NGUYEN NGOC TUNG',
                            'account_number' => 'VQRQADWLF2921',
                            'amount' => $data['amount'],
                            'content' => $data['transaction_id'] ?? 'DEMO'
                        ]
                    ];
                } else {
                    Log::error('PayOS API returned error', [
                        'code' => $result['code'] ?? 'UNKNOWN',
                        'message' => $result['message'] ?? 'Unknown error',
                        'transaction_id' => $data['transaction_id'] ?? null
                    ]);

                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Lỗi tạo QR code',
                        'error_code' => $result['code'] ?? 'UNKNOWN'
                    ];
                }
            } else {
                Log::error('PayOS API HTTP error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'transaction_id' => $data['transaction_id'] ?? null
                ]);

                return [
                    'success' => false,
                    'message' => 'Lỗi kết nối PayOS API: HTTP ' . $response->status(),
                    'error_code' => 'HTTP_' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('PayOS API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $data['transaction_id'] ?? null
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi kết nối PayOS API: ' . $e->getMessage(),
                'error_code' => 'EXCEPTION'
            ];
        }
    }

    /**
     * Tạo signature cho PayOS API
     */
    private function generatePayOSSignature($data)
    {
        $orderCode = $data['transaction_id'] ?? 'ORDER_' . time();
        $amount = $data['amount'];
        $description = $data['message'] ?? 'Thanh toán gói dịch vụ';

        // PayOS signature format: orderCode + amount + description + checksumKey
        $signatureString = $orderCode . $amount . $description . $this->checksumKey;

        Log::info('PayOS Signature Generation', [
            'orderCode' => $orderCode,
            'amount' => $amount,
            'description' => $description,
            'signatureString' => $signatureString,
            'checksumKey' => substr($this->checksumKey, 0, 10) . '...' // Chỉ log một phần để bảo mật
        ]);

        // Sử dụng HMAC SHA256 với checksum key
        $signature = hash_hmac('sha256', $signatureString, $this->checksumKey);

        Log::info('PayOS Signature Generated', [
            'signature' => $signature
        ]);

        return $signature;
    }

    /**
     * Tạo QR code demo khi API không hoạt động
     */
    private function generateDemoQrCode($data)
    {
        try {
            Log::info('Generating demo QR code', [
                'amount' => $data['amount'],
                'transaction_id' => $data['transaction_id'] ?? 'DEMO'
            ]);

            // Tạo VIETQR string chuẩn
            $vietqrString = "000201010212" .
                "38580010A00000072701290006970422" .
                "0112VQRQADWLF2921" .
                "5204597053037045408" .
                sprintf("%08d", $data['amount']) .
                "5802VN62" .
                sprintf("%02d", strlen($data['transaction_id'])) . $data['transaction_id'] .
                "6304";

            // Tạo QR code bằng Google Charts API
            $qrImageUrl = "https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=" . urlencode($vietqrString);

            Log::info('Demo QR code generated successfully', [
                'transaction_id' => $data['transaction_id'] ?? 'DEMO',
                'amount' => $data['amount']
            ]);

            return [
                'success' => true,
                'qr_code' => $qrImageUrl,
                'qr_text' => $vietqrString,
                'transaction_id' => $data['transaction_id'] ?? 'DEMO',
                'amount' => $data['amount'],
                'message' => 'QR code demo tạo thành công',
                'payment_url' => 'https://pay.payos.vn/web/1cc1a2b904054810b8290d966bf0db1d',
                'is_demo' => true,
                'payos_info' => [
                    'bank' => 'Ngân hàng TMCP Quân đội',
                    'account_name' => 'NGUYEN NGOC TUNG',
                    'account_number' => 'VQRQADWLF2921',
                    'amount' => $data['amount'],
                    'content' => $data['transaction_id'] ?? 'DEMO',
                    'note' => 'Demo Mode - Chỉ để test'
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error generating demo QR code', [
                'message' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'success' => false,
                'message' => 'Không thể tạo QR code. Vui lòng liên hệ hỗ trợ.',
                'error' => $e->getMessage()
            ];
        }
    }

    public function generateVietQrCode($data)
    {
        try {
            // Tạo thông tin VIETQR
            $vietqrData = [
                'bank' => 'Quan Doi (MB)',
                'account' => 'VQRQADWLF2921',
                'name' => 'NGUYEN NGOC TUNG',
                'amount' => $data['amount'] ?? 1000,
                'content' => $data['orderCode'] ?? 'TXN_' . uniqid()
            ];

            // Tạo chuỗi VIETQR theo format chuẩn
            $vietqrString = $this->createVietQrString($vietqrData);

            Log::info('Generated VIETQR string: ' . $vietqrString);

            // Sử dụng Google Charts API để tạo QR code
            $qrImageUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($vietqrString);

            return [
                'success' => true,
                'qr_code' => $qrImageUrl,
                'vietqr_data' => $vietqrData,
                'vietqr_string' => $vietqrString
            ];
        } catch (\Exception $e) {
            Log::error('Error generating VIETQR code: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Không thể tạo QR code VIETQR: ' . $e->getMessage()
            ];
        }
    }

    private function createVietQrString($data)
    {
        // Format VIETQR theo chuẩn EMV QR Code
        $bankBin = '970422'; // Mã ngân hàng MB
        $accountNo = $data['account'];
        $amount = $data['amount'];
        $content = $data['content'];

        // Tạo chuỗi VIETQR theo format EMV QR Code
        $vietqrString = "000201010212";
        $vietqrString .= "38580010A00000072701290006" . $bankBin . "0112" . $accountNo;
        $vietqrString .= "52045" . $bankBin;
        $vietqrString .= "5303704";
        $vietqrString .= "54" . strlen($amount) . $amount;
        $vietqrString .= "5802VN";
        $vietqrString .= "62" . strlen($content) . $content;
        $vietqrString .= "6304";

        return $vietqrString;
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkPaymentStatus($transactionId)
    {
        // Nếu đang ở demo mode, trả về kết quả demo
        if (config('vietqr.api.use_demo_mode', false)) {
            Log::info('Using demo mode for payment status check', [
                'transaction_id' => $transactionId
            ]);

            // Trong demo mode, giả lập thanh toán thành công sau 2 phút
            $subscription = \App\Models\UserSubscription::where('transaction_id', $transactionId)->first();

            if ($subscription) {
                $timeSinceCreated = now()->diffInMinutes($subscription->created_at);

                // Giả lập thanh toán thành công sau 2 phút
                if ($timeSinceCreated >= 2) {
                    return [
                        'success' => true,
                        'status' => 'completed',
                        'amount' => $subscription->amount,
                        'message' => 'Thanh toán demo thành công',
                        'is_demo' => true
                    ];
                } else {
                    return [
                        'success' => false,
                        'status' => 'pending',
                        'message' => 'Thanh toán demo đang chờ xử lý',
                        'is_demo' => true
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Không tìm thấy giao dịch demo',
                'is_demo' => true
            ];
        }

        try {
            $endpoint = str_replace('{id}', $transactionId, config('vietqr.api.check_status_endpoint'));
            $url = $this->baseUrl . $endpoint;

            $headers = [
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ];

            Log::info('Checking payment status via PayOS API', [
                'url' => $url,
                'transaction_id' => $transactionId
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->get($url);

            Log::info('PayOS API Response for status check', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['code']) && $result['code'] === '00') {
                    return [
                        'success' => true,
                        'status' => $result['data']['status'] ?? 'unknown',
                        'amount' => $result['data']['amount'] ?? 0,
                        'message' => $result['data']['message'] ?? 'Thanh toán thành công'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Không thể kiểm tra trạng thái thanh toán'
            ];

        } catch (\Exception $e) {
            Log::error('PayOS Check Status Error', [
                'message' => $e->getMessage(),
                'transaction_id' => $transactionId
            ]);

            return [
                'success' => false,
                'message' => 'Lỗi kiểm tra trạng thái: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy danh sách ngân hàng
     */
    public function getBanks()
    {
        return config('vietqr.banks', []);
    }
}
