<?php

echo "🔧 Sửa lỗi Google OAuth Redirect URI\n";
echo "=====================================\n\n";

// Kiểm tra file .env
$envFile = '.env';
if (!file_exists($envFile)) {
    echo "❌ Không tìm thấy file .env\n";
    exit(1);
}

$envContent = file_get_contents($envFile);

// Kiểm tra APP_URL
if (strpos($envContent, 'APP_URL=http://127.0.0.1:8000') === false) {
    echo "⚠️  APP_URL chưa được cấu hình đúng\n";
    echo "Đang thêm APP_URL=http://127.0.0.1:8000...\n";

    if (strpos($envContent, 'APP_URL=') === false) {
        $envContent .= "\nAPP_URL=http://127.0.0.1:8000";
    } else {
        $envContent = preg_replace('/APP_URL=.*/', 'APP_URL=http://127.0.0.1:8000', $envContent);
    }

    file_put_contents($envFile, $envContent);
    echo "✅ Đã cập nhật APP_URL\n";
} else {
    echo "✅ APP_URL đã được cấu hình đúng\n";
}

// Kiểm tra Google OAuth config
if (strpos($envContent, 'GOOGLE_CLIENT_ID=') === false) {
    echo "❌ GOOGLE_CLIENT_ID chưa được cấu hình\n";
    exit(1);
}

if (strpos($envContent, 'GOOGLE_CLIENT_SECRET=') === false) {
    echo "❌ GOOGLE_CLIENT_SECRET chưa được cấu hình\n";
    exit(1);
}

echo "✅ Google OAuth credentials đã được cấu hình\n";

// Hiển thị thông tin quan trọng
echo "\n📋 Thông tin quan trọng:\n";
echo "========================\n";
echo "1. Server URL: http://127.0.0.1:8000\n";
echo "2. Google OAuth Redirect URI: http://127.0.0.1:8000/auth/google/callback\n";
echo "3. Login URL: http://127.0.0.1:8000/login\n";
echo "4. Direct Google OAuth URL: http://127.0.0.1:8000/auth/google\n\n";

echo "🔗 Cấu hình Google Console:\n";
echo "===========================\n";
echo "Trong Google Cloud Console, hãy đảm bảo:\n";
echo "- Authorized redirect URIs có: http://127.0.0.1:8000/auth/google/callback\n";
echo "- KHÔNG sử dụng localhost, phải dùng 127.0.0.1:8000\n\n";

echo "🚀 Hướng dẫn test:\n";
echo "==================\n";
echo "1. Khởi động server: php artisan serve\n";
echo "2. Truy cập: http://127.0.0.1:8000/login\n";
echo "3. Click 'Đăng nhập bằng Google'\n";
echo "4. Hoặc truy cập trực tiếp: http://127.0.0.1:8000/auth/google\n\n";

echo "⚠️  Lưu ý quan trọng:\n";
echo "====================\n";
echo "- KHÔNG sử dụng localhost, phải dùng 127.0.0.1:8000\n";
echo "- Nếu vẫn lỗi, hãy kiểm tra Google Console redirect URI\n";
echo "- Đảm bảo Google OAuth credentials đúng\n\n";

echo "✅ Script hoàn thành!\n";