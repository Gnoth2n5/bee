<?php

/**
 * Script tự động tạo Gemini API Key mới
 * Hướng dẫn chi tiết từng bước
 */

echo "🔑 Tự động tạo Gemini API Key mới...\n\n";

echo "📋 HƯỚNG DẪN CHI TIẾT:\n";
echo "=====================================\n\n";

echo "🎯 BƯỚC 1: Tạo tài khoản Google mới\n";
echo "1. Mở trình duyệt ẩn danh (Ctrl+Shift+N)\n";
echo "2. Truy cập: https://accounts.google.com/signup\n";
echo "3. Tạo tài khoản Google mới với:\n";
echo "   - Email: bee.recipe.ai.1@gmail.com (hoặc tương tự)\n";
echo "   - Mật khẩu: Mạnh và an toàn\n";
echo "   - Số điện thoại: Có thể dùng số thật\n";
echo "4. Xác minh email và số điện thoại\n\n";

echo "🎯 BƯỚC 2: Tạo API Key\n";
echo "1. Đăng nhập vào tài khoản Google mới\n";
echo "2. Truy cập: https://aistudio.google.com/app/apikey\n";
echo "3. Click 'Create API Key'\n";
echo "4. Chọn 'Create API Key in new project'\n";
echo "5. Đặt tên project: 'Bee Recipe AI - Key 1'\n";
echo "6. Click 'Create'\n";
echo "7. Copy API Key (bắt đầu bằng AIzaSy...)\n\n";

echo "🎯 BƯỚC 3: Test API Key\n";
echo "1. Paste API Key vào đây\n";
echo "2. Script sẽ tự động test và cập nhật\n";
echo "3. Khởi động server nếu thành công\n\n";

echo "💡 TIPS QUAN TRỌNG:\n";
echo "- Mỗi tài khoản Google có 15 requests/phút\n";
echo "- 1000 requests/ngày miễn phí\n";
echo "- Tạo nhiều tài khoản để có nhiều quota\n";
echo "- Sử dụng email thật để tránh bị khóa\n\n";

echo "🚀 BẮT ĐẦU:\n";
echo "Paste API Key mới vào đây: ";

// Đọc input từ user
$handle = fopen("php://stdin", "r");
$apiKey = trim(fgets($handle));
fclose($handle);

if (!empty($apiKey) && strpos($apiKey, 'AIzaSy') === 0) {
    echo "\n🔄 Đang xử lý API Key...\n";

    // Test API key trước
    echo "🧪 Test API Key...\n";
    $testResult = testApiKey($apiKey);

    if ($testResult['status'] === 'working') {
        echo "✅ API Key hoạt động tốt!\n";

        // Cập nhật vào .env
        updateEnvWithKey($apiKey);
        echo "✅ Đã cập nhật API Key vào .env\n";

        // Lưu vào danh sách keys
        saveKeyToList($apiKey);
        echo "✅ Đã lưu API Key vào danh sách\n";

        // Khởi động server
        echo "🚀 Khởi động server...\n";
        exec('php artisan serve', $output, $returnCode);

        if ($returnCode === 0) {
            echo "✅ Server đã khởi động thành công!\n";
            echo "🌐 Truy cập: http://localhost:8000\n";
            echo "🎉 Tính năng tìm kiếm bằng ảnh đã sẵn sàng!\n";
        }
    } else {
        echo "❌ API Key có vấn đề: " . $testResult['message'] . "\n";
        echo "💡 Vui lòng tạo API Key mới hoặc thử lại sau.\n";
    }
} else {
    echo "\n❌ API Key không hợp lệ!\n";
    echo "API Key phải bắt đầu bằng 'AIzaSy'\n";
    echo "Vui lòng chạy lại script và nhập API Key đúng.\n";
}

function testApiKey($apiKey)
{
    $baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro-latest:generateContent";

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => "Hello, this is a test message."]
                ]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . "?key=" . $apiKey);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result["candidates"][0]["content"]["parts"][0]["text"])) {
        return ['status' => 'working', 'message' => 'Hoạt động tốt'];
    } elseif ($httpCode === 429) {
        return ['status' => 'quota_exceeded', 'message' => 'Hết quota'];
    } elseif ($httpCode === 400 && strpos($response, "expired") !== false) {
        return ['status' => 'expired', 'message' => 'Đã hết hạn'];
    } elseif ($httpCode === 403) {
        return ['status' => 'forbidden', 'message' => 'API Key không hợp lệ'];
    } else {
        return ['status' => 'error', 'message' => 'Lỗi HTTP ' . $httpCode];
    }
}

function updateEnvWithKey($key)
{
    $envContent = file_get_contents('.env');
    $envContent = preg_replace('/GEMINI_API_KEY=.*/', 'GEMINI_API_KEY=' . $key, $envContent);
    file_put_contents('.env', $envContent);
    exec('php artisan config:clear');
}

function saveKeyToList($key)
{
    $keysFile = 'working_keys.json';
    $keys = [];

    if (file_exists($keysFile)) {
        $keys = json_decode(file_get_contents($keysFile), true);
    }

    $keys[] = [
        'key' => $key,
        'created_at' => date('Y-m-d H:i:s'),
        'status' => 'working'
    ];

    file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT));
}

echo "\n🎯 HƯỚNG DẪN TIẾP THEO:\n";
echo "1. Nếu thành công: Tính năng tìm kiếm bằng ảnh sẽ hoạt động\n";
echo "2. Nếu thất bại: Tạo tài khoản Google khác và thử lại\n";
echo "3. Để tạo nhiều keys: Chạy php auto_create_keys.php\n";
echo "4. Để test keys hiện có: Chạy php find_working_key.php\n";

?>