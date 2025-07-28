<?php

/**
 * Script để test Google OAuth trực tiếp
 * Chạy: php test_google_oauth.php
 */

require_once 'vendor/autoload.php';

use Laravel\Socialite\Facades\Socialite;

echo "🧪 Test Google OAuth Configuration\n";
echo "==================================\n\n";

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Kiểm tra config
    $clientId = config('services.google.client_id');
    $clientSecret = config('services.google.client_secret');
    $redirectUrl = config('services.google.redirect');

    echo "📋 Configuration:\n";
    echo "Client ID: " . ($clientId ?: '❌ Không có') . "\n";
    echo "Client Secret: " . ($clientSecret ?: '❌ Không có') . "\n";
    echo "Redirect URL: " . ($redirectUrl ?: '❌ Không có') . "\n\n";

    if (!$clientId || !$clientSecret) {
        echo "❌ VẤN ĐỀ: Thiếu Client ID hoặc Client Secret!\n";
        echo "Hãy cập nhật file .env với thông tin thực từ Google Console.\n";
        exit(1);
    }

    // Test Socialite configuration
    echo "🔧 Testing Socialite configuration...\n";

    $socialite = Socialite::driver('google');
    $socialite->redirectUrl($redirectUrl);

    echo "✅ Socialite configuration OK\n";
    echo "✅ Redirect URL: " . $redirectUrl . "\n";
    echo "✅ Client ID: " . substr($clientId, 0, 20) . "...\n";
    echo "✅ Client Secret: " . substr($clientSecret, 0, 10) . "...\n\n";

    echo "🎯 Test URL để đăng nhập Google:\n";
    echo "http://127.0.0.1:8000/auth/google\n\n";

    echo "📝 Hướng dẫn test:\n";
    echo "1. Khởi động server: php artisan serve\n";
    echo "2. Truy cập: http://127.0.0.1:8000/login\n";
    echo "3. Click nút 'Đăng nhập bằng Google'\n";
    echo "4. Hoặc truy cập trực tiếp: http://127.0.0.1:8000/auth/google\n\n";

    echo "🔗 Google Console cần cấu hình:\n";
    echo "- Authorized Redirect URIs: " . $redirectUrl . "\n";
    echo "- Client ID: " . $clientId . "\n";

} catch (Exception $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}