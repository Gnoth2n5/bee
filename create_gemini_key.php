<?php

/**
 * Script tự động tạo Gemini API Key
 * Sử dụng Google AI Studio để tạo key mới
 */

echo "🔑 Đang tạo Gemini API Key mới...\n\n";

// URL Google AI Studio
$studioUrl = "https://aistudio.google.com/app/apikey";

echo "📋 Hướng dẫn tạo API Key:\n";
echo "1. Truy cập: {$studioUrl}\n";
echo "2. Đăng nhập bằng Google Account\n";
echo "3. Click 'Create API Key'\n";
echo "4. Chọn 'Create API Key in new project'\n";
echo "5. Đặt tên project (ví dụ: 'Bee Recipe AI')\n";
echo "6. Copy API Key và paste vào đây\n\n";

echo "🎯 Lưu ý quan trọng:\n";
echo "- Mỗi tài khoản Google có 15 requests/phút miễn phí\n";
echo "- 1000 requests/ngày miễn phí\n";
echo "- Nếu hết quota, tạo tài khoản Google mới\n\n";

echo "💡 Tips để tránh hết quota:\n";
echo "- Sử dụng ít requests hơn\n";
echo "- Cache kết quả phân tích ảnh\n";
echo "- Nâng cấp lên plan trả phí\n\n";

echo "🚀 Sau khi có API Key, chạy lệnh:\n";
echo "php update_key.php YOUR_NEW_API_KEY\n\n";

echo "✅ Hoặc paste API Key vào đây để tôi cập nhật tự động:\n";
echo "API Key: ";

// Đọc input từ user
$handle = fopen("php://stdin", "r");
$apiKey = trim(fgets($handle));
fclose($handle);

if (!empty($apiKey)) {
    echo "\n🔄 Đang cập nhật API Key...\n";
    
    // Cập nhật file .env
    $envContent = file_get_contents('.env');
    $envContent = preg_replace('/GEMINI_API_KEY=.*/', 'GEMINI_API_KEY=' . $apiKey, $envContent);
    file_put_contents('.env', $envContent);
    
    echo "✅ Đã cập nhật API Key vào .env\n";
    
    // Clear config cache
    exec('php artisan config:clear');
    echo "✅ Đã clear config cache\n";
    
    // Test API key
    echo "🧪 Đang test API Key...\n";
    exec('php test_gemini_api.php', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "🎉 API Key hoạt động tốt!\n";
        echo "🚀 Khởi động server...\n";
        exec('php artisan serve');
    } else {
        echo "❌ API Key có vấn đề. Vui lòng kiểm tra lại.\n";
    }
} else {
    echo "\n❌ Không có API Key được nhập.\n";
    echo "Vui lòng chạy lại script và nhập API Key.\n";
}

?> 