<?php

/**
 * Script để cập nhật Google OAuth credentials với thông tin thực
 * Chạy: php update_google_credentials_final.php
 */

echo "🔧 Cập nhật Google OAuth Credentials (Final)\n";
echo "===========================================\n\n";

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "❌ File .env không tồn tại!\n";
    exit(1);
}

// Thông tin thực từ Google Console
$clientId = '254134291341-t0gq62vrnqesoo4tuto0p3c5bfp6homh.apps.googleusercontent.com';
$clientSecret = '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';

echo "📋 Thông tin cập nhật:\n";
echo "Client ID: " . $clientId . "\n";
echo "Client Secret: " . $clientSecret . "\n\n";

// Đọc file .env hiện tại
$envContent = file_get_contents($envPath);

// Cập nhật Client ID
$envContent = preg_replace(
    '/GOOGLE_CLIENT_ID=.*/',
    'GOOGLE_CLIENT_ID=' . $clientId,
    $envContent
);

// Cập nhật Client Secret
$envContent = preg_replace(
    '/GOOGLE_CLIENT_SECRET=.*/',
    'GOOGLE_CLIENT_SECRET=' . $clientSecret,
    $envContent
);

// Ghi lại file .env
if (file_put_contents($envPath, $envContent)) {
    echo "✅ Đã cập nhật Google OAuth credentials thành công!\n\n";

    echo "📝 Bước tiếp theo:\n";
    echo "1. Clear cache:\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan cache:clear\n";
    echo "2. Kiểm tra cấu hình:\n";
    echo "   php artisan google:check-credentials\n";
    echo "3. Test chức năng:\n";
    echo "   php artisan serve\n";
    echo "4. Truy cập: http://127.0.0.1:8000/login\n";
    echo "5. Click nút 'Đăng nhập bằng Google'\n\n";

    echo "🔗 Google Console cần cấu hình:\n";
    echo "- Authorized Redirect URIs: http://127.0.0.1:8000/auth/google/callback\n";
    echo "- Client ID: " . $clientId . "\n";
    echo "- Client Secret: " . $clientSecret . "\n\n";

    echo "🎉 Google OAuth đã sẵn sàng sử dụng!\n";
} else {
    echo "❌ Không thể cập nhật file .env\n";
    exit(1);
}