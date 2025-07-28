<?php

/**
 * Script để kiểm tra Google OAuth credentials
 * Chạy: php check_google_credentials.php
 */

echo "🔍 Kiểm tra Google OAuth Credentials\n";
echo "=====================================\n\n";

// Đọc file .env
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "❌ File .env không tồn tại!\n";
    exit(1);
}

$envContent = file_get_contents($envPath);

// Tìm Google credentials
preg_match('/GOOGLE_CLIENT_ID=(.*)/', $envContent, $clientIdMatches);
preg_match('/GOOGLE_CLIENT_SECRET=(.*)/', $envContent, $clientSecretMatches);

$clientId = trim($clientIdMatches[1] ?? '');
$clientSecret = trim($clientSecretMatches[1] ?? '');

echo "📋 Thông tin hiện tại:\n";
echo "Client ID: " . ($clientId ?: '❌ Không tìm thấy') . "\n";
echo "Client Secret: " . ($clientSecret ?: '❌ Không tìm thấy') . "\n\n";

// Kiểm tra xem có phải placeholder không
if ($clientId === 'your_client_id_here' || $clientSecret === 'your_client_secret_here') {
    echo "❌ VẤN ĐỀ: Bạn vẫn đang sử dụng placeholder!\n";
    echo "Hãy thay thế bằng thông tin thực từ Google Console.\n\n";

    echo "🔧 CÁCH FIX:\n";
    echo "1. Truy cập: https://console.cloud.google.com/\n";
    echo "2. Vào 'APIs & Services' > 'Credentials'\n";
    echo "3. Tạo hoặc chọn OAuth 2.0 Client ID\n";
    echo "4. Copy Client ID và Client Secret\n";
    echo "5. Cập nhật file .env:\n\n";

    echo "Thay thế:\n";
    echo "GOOGLE_CLIENT_ID=your_client_id_here\n";
    echo "GOOGLE_CLIENT_SECRET=your_client_secret_here\n\n";

    echo "Thành:\n";
    echo "GOOGLE_CLIENT_ID=123456789-abcdefghijklmnop.apps.googleusercontent.com\n";
    echo "GOOGLE_CLIENT_SECRET=GOCSPX-abcdefghijklmnopqrstuvwxyz\n\n";

    echo "6. Clear cache:\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan cache:clear\n\n";

    echo "7. Test lại:\n";
    echo "   php artisan google:check-config\n";

    exit(1);
}

// Kiểm tra format của Client ID
if (!preg_match('/^\d+-\w+\.apps\.googleusercontent\.com$/', $clientId)) {
    echo "⚠️ CẢNH BÁO: Client ID có vẻ không đúng format!\n";
    echo "Client ID thường có format: 123456789-abcdefghijklmnop.apps.googleusercontent.com\n\n";
}

// Kiểm tra format của Client Secret
if (!preg_match('/^GOCSPX-/', $clientSecret)) {
    echo "⚠️ CẢNH BÁO: Client Secret có vẻ không đúng format!\n";
    echo "Client Secret thường bắt đầu bằng: GOCSPX-\n\n";
}

echo "✅ Credentials đã được cấu hình!\n";
echo "🧪 Test chức năng:\n";
echo "1. php artisan serve\n";
echo "2. Truy cập: http://127.0.0.1:8000/login\n";
echo "3. Click nút 'Đăng nhập bằng Google'\n\n";

echo "📞 Nếu vẫn gặp lỗi:\n";
echo "- Kiểm tra Google Console: Redirect URI phải là http://127.0.0.1:8000/auth/google/callback\n";
echo "- Đảm bảo OAuth 2.0 Client ID đã được tạo đúng\n";
echo "- Kiểm tra logs: storage/logs/laravel.log\n";