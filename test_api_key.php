<?php

// Test API key Gemini
$apiKey = 'AIzaSyBxDDfR6EVVidKdRz8rgBmdiEPkNKF9YNM';

echo "=== Test Gemini API Key ===\n";
echo "API Key: " . substr($apiKey, 0, 10) . "...\n";

// Test cấu hình
$config = [
    'gemini' => [
        'api_key' => $apiKey,
    ]
];

echo "✅ API Key đã được cấu hình\n";
echo "🎯 Bây giờ bạn có thể test tính năng tìm kiếm bằng ảnh!\n";
echo "\n📋 Hướng dẫn:\n";
echo "1. Truy cập: http://localhost:8000\n";
echo "2. Click icon camera 📷 bên cạnh ô tìm kiếm\n";
echo "3. Chọn ảnh món ăn\n";
echo "4. Click 'Phân tích ảnh'\n";
echo "5. Xem kết quả!\n";