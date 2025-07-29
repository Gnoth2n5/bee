<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Liệt kê Gemini Models ===\n\n";

// Lấy API key
$apiKey = config('services.gemini.api_key');
echo "API Key: " . substr($apiKey, 0, 10) . "...\n\n";

if (!$apiKey) {
    echo "❌ Lỗi: Chưa cấu hình GEMINI_API_KEY\n";
    exit(1);
}

// Thử các endpoint khác nhau
$endpoints = [
    'v1beta' => 'https://generativelanguage.googleapis.com/v1beta/models',
    'v1' => 'https://generativelanguage.googleapis.com/v1/models',
];

foreach ($endpoints as $version => $url) {
    echo "🔄 Kiểm tra $version...\n";

    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(30)->get($url . '?key=' . $apiKey);

        echo "Status Code: " . $response->status() . "\n";

        if ($response->successful()) {
            echo "✅ $version thành công!\n";
            $data = $response->json();

            if (isset($data['models'])) {
                echo "📋 Danh sách models:\n";
                foreach ($data['models'] as $model) {
                    $name = $model['name'] ?? 'Unknown';
                    $displayName = $model['displayName'] ?? 'No display name';
                    $description = $model['description'] ?? 'No description';

                    echo "- $name ($displayName): $description\n";
                }
            } else {
                echo "⚠️ Không có models trong response\n";
                echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            }
        } else {
            echo "❌ $version thất bại: " . $response->body() . "\n";
        }

    } catch (\Exception $e) {
        echo "❌ Exception cho $version: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

echo "🎯 Hoàn tất!\n";