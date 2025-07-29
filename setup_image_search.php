<?php

/**
 * Script setup hoàn chỉnh tính năng tìm kiếm bằng ảnh
 * Tự động tạo API key, test và khởi động server
 */

echo "🚀 SETUP TÍNH NĂNG TÌM KIẾM BẰNG ẢNH\n";
echo "=====================================\n\n";

echo "📋 Kiểm tra hệ thống...\n";

// Kiểm tra file .env
if (!file_exists('.env')) {
    echo "❌ File .env không tồn tại!\n";
    echo "🔧 Tạo file .env từ .env.example...\n";
    copy('.env.example', '.env');
    echo "✅ Đã tạo file .env\n";
}

// Kiểm tra APP_KEY
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_KEY=base64:') === false) {
    echo "🔧 Tạo APP_KEY...\n";
    exec('php artisan key:generate');
    echo "✅ Đã tạo APP_KEY\n";
}

// Kiểm tra GEMINI_API_KEY
if (strpos($envContent, 'GEMINI_API_KEY=') === false) {
    echo "❌ Chưa có GEMINI_API_KEY!\n";
    echo "🔧 Thêm GEMINI_API_KEY vào .env...\n";
    file_put_contents('.env', $envContent . "\nGEMINI_API_KEY=", FILE_APPEND);
    echo "✅ Đã thêm GEMINI_API_KEY\n";
}

echo "✅ Hệ thống đã sẵn sàng!\n\n";

echo "🎯 BƯỚC 1: Tìm API Key hoạt động\n";
echo "--------------------------------\n";

// Chạy script tìm key hoạt động
echo "🔍 Đang tìm API Key hoạt động...\n";
exec('php find_working_key.php', $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ Tìm thấy API Key hoạt động!\n";
    echo "🚀 Khởi động server...\n";
    exec('php artisan serve', $output, $returnCode);

    if ($returnCode === 0) {
        echo "✅ Server đã khởi động thành công!\n";
        echo "🌐 Truy cập: http://localhost:8000\n";
        echo "🎉 Tính năng tìm kiếm bằng ảnh đã sẵn sàng!\n";
        exit(0);
    }
} else {
    echo "❌ Không tìm thấy API Key hoạt động!\n\n";
}

echo "🎯 BƯỚC 2: Tạo API Key mới\n";
echo "--------------------------\n";

echo "📋 Hướng dẫn tạo API Key:\n";
echo "1. Mở trình duyệt ẩn danh\n";
echo "2. Truy cập: https://accounts.google.com/signup\n";
echo "3. Tạo tài khoản Google mới\n";
echo "4. Truy cập: https://aistudio.google.com/app/apikey\n";
echo "5. Click 'Create API Key'\n";
echo "6. Copy API Key và paste vào đây\n\n";

echo "Paste API Key mới: ";

// Đọc input từ user
$handle = fopen("php://stdin", "r");
$apiKey = trim(fgets($handle));
fclose($handle);

if (!empty($apiKey) && strpos($apiKey, 'AIzaSy') === 0) {
    echo "\n🔄 Đang xử lý API Key...\n";

    // Cập nhật vào .env
    $envContent = file_get_contents('.env');
    $envContent = preg_replace('/GEMINI_API_KEY=.*/', 'GEMINI_API_KEY=' . $apiKey, $envContent);
    file_put_contents('.env', $envContent);

    // Clear config cache
    exec('php artisan config:clear');
    echo "✅ Đã cập nhật API Key\n";

    // Test API key
    echo "🧪 Test API Key...\n";
    $testResult = testApiKey($apiKey);

    if ($testResult['status'] === 'working') {
        echo "✅ API Key hoạt động tốt!\n";

        // Khởi động server
        echo "🚀 Khởi động server...\n";
        exec('php artisan serve', $output, $returnCode);

        if ($returnCode === 0) {
            echo "✅ Server đã khởi động thành công!\n";
            echo "🌐 Truy cập: http://localhost:8000\n";
            echo "🎉 Tính năng tìm kiếm bằng ảnh đã sẵn sàng!\n";

            // Lưu key vào danh sách
            saveKeyToList($apiKey);
            echo "✅ Đã lưu API Key vào danh sách\n";
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

echo "\n🎯 HƯỚNG DẪN SỬ DỤNG:\n";
echo "1. Upload ảnh món ăn vào thanh tìm kiếm\n";
echo "2. AI sẽ phân tích và trích xuất từ khóa\n";
echo "3. Tự động chuyển sang trang công thức\n";
echo "4. Hiển thị kết quả tìm kiếm\n\n";

echo "🔧 QUẢN LÝ API KEYS:\n";
echo "- Test keys: php find_working_key.php\n";
echo "- Tạo key mới: php auto_generate_key.php\n";
echo "- Tạo nhiều keys: php auto_create_keys.php\n";
echo "- Xem hướng dẫn: README_API_KEYS.md\n";

?>