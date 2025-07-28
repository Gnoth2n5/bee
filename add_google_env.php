<?php

/**
 * Script để thêm Google OAuth config vào file .env
 * Chạy: php add_google_env.php
 */

$envPath = __DIR__ . '/.env';

if (!file_exists($envPath)) {
    echo "❌ File .env không tồn tại!\n";
    echo "Hãy tạo file .env trước khi chạy script này.\n";
    exit(1);
}

// Đọc nội dung file .env hiện tại
$envContent = file_get_contents($envPath);

// Kiểm tra xem đã có Google config chưa
if (strpos($envContent, 'GOOGLE_CLIENT_ID') !== false) {
    echo "⚠️ Google OAuth config đã tồn tại trong file .env\n";
    echo "Hãy kiểm tra và cập nhật thông tin credentials.\n";
    exit(0);
}

// Thêm Google OAuth config
$googleConfig = "\n# Google OAuth Configuration\n";
$googleConfig .= "# Thay thế your_client_id_here và your_client_secret_here bằng thông tin thực từ Google Console\n";
$googleConfig .= "GOOGLE_CLIENT_ID=your_client_id_here\n";
$googleConfig .= "GOOGLE_CLIENT_SECRET=your_client_secret_here\n";

// Thêm vào cuối file .env
$newEnvContent = $envContent . $googleConfig;

// Ghi lại file .env
if (file_put_contents($envPath, $newEnvContent)) {
    echo "✅ Đã thêm Google OAuth config vào file .env\n";
    echo "\n📝 Bước tiếp theo:\n";
    echo "1. Truy cập https://console.cloud.google.com/\n";
    echo "2. Tạo OAuth 2.0 Client ID\n";
    echo "3. Copy Client ID và Client Secret\n";
    echo "4. Thay thế your_client_id_here và your_client_secret_here trong file .env\n";
    echo "5. Chạy: php artisan config:clear\n";
    echo "6. Chạy: php artisan cache:clear\n";
    echo "7. Test: php artisan google:check-config\n";
} else {
    echo "❌ Không thể ghi file .env\n";
    echo "Hãy kiểm tra quyền ghi file.\n";
    exit(1);
}