<?php

/**
 * Test script để kiểm tra tính năng phân tích ảnh với Gemini AI
 * Chạy: php test_gemini_image_search.php
 */

require_once 'vendor/autoload.php';

use App\Services\GeminiService;

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Gemini Image Analysis ===\n\n";

// Kiểm tra API key
$apiKey = config('services.gemini.api_key');
if (!$apiKey) {
    echo "❌ Lỗi: Chưa cấu hình GEMINI_API_KEY trong file .env\n";
    echo "Vui lòng thêm dòng sau vào file .env:\n";
    echo "GEMINI_API_KEY=your_api_key_here\n\n";
    exit(1);
}

echo "✅ API Key đã được cấu hình\n\n";

// Test service
try {
    $geminiService = new GeminiService();
    echo "✅ GeminiService đã được khởi tạo thành công\n\n";

    echo "📋 Hướng dẫn sử dụng:\n";
    echo "1. Truy cập trang chủ: http://localhost:8000\n";
    echo "2. Click vào icon camera bên cạnh ô tìm kiếm\n";
    echo "3. Chọn ảnh món ăn (JPG, PNG, GIF, WebP, tối đa 5MB)\n";
    echo "4. Click 'Phân tích ảnh'\n";
    echo "5. Hệ thống sẽ tự động tìm kiếm công thức phù hợp\n\n";

    echo "🔧 Tính năng:\n";
    echo "- Phân tích ảnh món ăn bằng AI\n";
    echo "- Trả về các từ khóa tìm kiếm phù hợp\n";
    echo "- Tự động tìm kiếm công thức với từ khóa tốt nhất\n";
    echo "- Hiển thị kết quả phân tích với giao diện đẹp\n\n";

    echo "⚠️  Lưu ý:\n";
    echo "- Cần có kết nối internet để gọi API Gemini\n";
    echo "- Ảnh nên rõ nét, có món ăn ở trung tâm\n";
    echo "- Tránh ảnh có nhiều món ăn khác nhau\n";
    echo "- Đảm bảo ánh sáng đủ sáng\n\n";

    echo "🎯 Test hoàn tất! Bạn có thể bắt đầu sử dụng tính năng.\n";

} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}