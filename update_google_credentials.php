<?php

/**
 * Script để cập nhật Google OAuth credentials
 * Chạy: php update_google_credentials.php
 */

echo "🔧 Cập nhật Google OAuth Credentials\n";
echo "====================================\n\n";

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "❌ File .env không tồn tại!\n";
    exit(1);
}

// Client ID từ Google Console
$clientId = '254134291341-rtm7v4ia5vsa54ckup7jo30ugl6uhjrr.apps.googleusercontent.com';

echo "📋 Thông tin cập nhật:\n";
echo "Client ID: " . $clientId . "\n";
echo "Client Secret: [Cần bạn cung cấp]\n\n";

// Đọc file .env hiện tại
$envContent = file_get_contents($envPath);

// Cập nhật Client ID
$envContent = preg_replace(
    '/GOOGLE_CLIENT_ID=.*/',
    'GOOGLE_CLIENT_ID=' . $clientId,
    $envContent
);

// Ghi lại file .env
if (file_put_contents($envPath, $envContent)) {
    echo "✅ Đã cập nhật Client ID thành công!\n\n";

    echo "📝 Bước tiếp theo:\n";
    echo "1. Lấy Client Secret từ Google Console\n";
    echo "2. Cập nhật GOOGLE_CLIENT_SECRET trong file .env\n";
    echo "3. Clear cache:\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan cache:clear\n";
    echo "4. Test: php artisan google:check-credentials\n\n";

    echo "🔗 Google Console: https://console.cloud.google.com/\n";
    echo "📁 File .env đã được cập nhật với Client ID mới\n";
} else {
    echo "❌ Không thể cập nhật file .env\n";
    exit(1);
}