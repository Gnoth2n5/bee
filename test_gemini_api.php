<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Gemini API thực tế ===\n\n";

// Lấy API key
$apiKey = config('services.gemini.api_key');
echo "API Key: " . substr($apiKey, 0, 10) . "...\n\n";

if (!$apiKey) {
    echo "❌ Lỗi: Chưa cấu hình GEMINI_API_KEY\n";
    exit(1);
}

// Test API với một ảnh mẫu (base64 của một ảnh nhỏ)
$testImageBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='; // 1x1 pixel PNG

$payload = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => 'Đây là ảnh món ăn. Hãy trả về danh sách các từ khóa tìm kiếm phù hợp để tìm công thức nấu ăn này. Trả về dưới dạng JSON array: ["từ khóa 1", "từ khóa 2", "từ khóa 3"]'
                ],
                [
                    'inline_data' => [
                        'mime_type' => 'image/png',
                        'data' => $testImageBase64
                    ]
                ]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.1,
        'topK' => 32,
        'topP' => 1,
        'maxOutputTokens' => 1024,
    ]
];

$baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent';

echo "🔄 Đang gọi API Gemini...\n";

try {
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->timeout(30)->post($baseUrl . '?key=' . $apiKey, $payload);

    echo "Status Code: " . $response->status() . "\n";

    if ($response->successful()) {
        echo "✅ API call thành công!\n";
        $data = $response->json();

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $data['candidates'][0]['content']['parts'][0]['text'];
            echo "📝 Response: " . $text . "\n";
        } else {
            echo "⚠️ Response không có text content\n";
            echo "Full response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "❌ API call thất bại!\n";
        echo "Error: " . $response->body() . "\n";
    }

} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n�� Test hoàn tất!\n";