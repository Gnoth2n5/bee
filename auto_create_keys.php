<?php

/**
 * Script tự động tạo nhiều Gemini API Key
 * Tạo key từ nhiều tài khoản Google để tránh hết quota
 */

echo "🔑 Tự động tạo Gemini API Keys...\n\n";

// Danh sách các tài khoản Google để tạo key
$accounts = [
    'account1@gmail.com',
    'account2@gmail.com',
    'account3@gmail.com',
    'account4@gmail.com',
    'account5@gmail.com'
];

echo "📋 Danh sách tài khoản để tạo API Key:\n";
foreach ($accounts as $index => $account) {
    echo ($index + 1) . ". {$account}\n";
}

echo "\n🎯 Hướng dẫn tạo API Key cho từng tài khoản:\n";
echo "1. Mở trình duyệt ẩn danh\n";
echo "2. Truy cập: https://aistudio.google.com/app/apikey\n";
echo "3. Đăng nhập bằng tài khoản Google\n";
echo "4. Click 'Create API Key'\n";
echo "5. Chọn 'Create API Key in new project'\n";
echo "6. Đặt tên project: 'Bee Recipe AI - Account X'\n";
echo "7. Copy API Key\n\n";

echo "💾 Lưu trữ API Keys:\n";
echo "- Mỗi tài khoản có 15 requests/phút\n";
echo "- 1000 requests/ngày miễn phí\n";
echo "- Tổng cộng: " . (count($accounts) * 15) . " requests/phút\n";
echo "- Tổng cộng: " . (count($accounts) * 1000) . " requests/ngày\n\n";

echo "🔄 Hệ thống tự động chuyển đổi API Key:\n";
echo "- Khi key hiện tại hết quota\n";
echo "- Tự động chuyển sang key tiếp theo\n";
echo "- Luân phiên sử dụng tất cả keys\n\n";

echo "📝 Tạo file lưu trữ API Keys:\n";

// Tạo file lưu trữ API Keys
$keysFile = 'api_keys.json';
$keys = [];

if (file_exists($keysFile)) {
    $keys = json_decode(file_get_contents($keysFile), true);
}

echo "✅ File {$keysFile} đã sẵn sàng\n";
echo "📊 Hiện có " . count($keys) . " API Keys được lưu\n\n";

echo "🚀 Bắt đầu nhập API Keys:\n";
echo "Nhập 'skip' để bỏ qua, 'done' để kết thúc\n\n";

$newKeys = [];
$counter = 1;

while (true) {
    echo "API Key {$counter}: ";

    $handle = fopen("php://stdin", "r");
    $input = trim(fgets($handle));
    fclose($handle);

    if (strtolower($input) === 'done') {
        break;
    }

    if (strtolower($input) === 'skip') {
        $counter++;
        continue;
    }

    if (!empty($input)) {
        $newKeys[] = $input;
        echo "✅ Đã lưu API Key {$counter}\n";
        $counter++;
    }
}

// Lưu API Keys mới
if (!empty($newKeys)) {
    $keys = array_merge($keys, $newKeys);
    file_put_contents($keysFile, json_encode($keys, JSON_PRETTY_PRINT));

    echo "\n🎉 Đã lưu " . count($newKeys) . " API Keys mới\n";
    echo "📊 Tổng cộng: " . count($keys) . " API Keys\n\n";

    // Tạo script tự động chuyển đổi API Key
    createKeyRotationScript($keys);

    // Cập nhật API Key đầu tiên vào .env
    if (!empty($keys)) {
        updateEnvWithKey($keys[0]);
        echo "✅ Đã cập nhật API Key đầu tiên vào .env\n";

        // Test API key
        echo "🧪 Đang test API Key...\n";
        exec('php test_gemini_api.php', $output, $returnCode);

        if ($returnCode === 0) {
            echo "🎉 API Key hoạt động tốt!\n";
        } else {
            echo "❌ API Key có vấn đề. Thử key tiếp theo...\n";
            if (count($keys) > 1) {
                updateEnvWithKey($keys[1]);
                echo "✅ Đã chuyển sang API Key thứ 2\n";
            }
        }
    }
} else {
    echo "\n❌ Không có API Key nào được nhập.\n";
}

function createKeyRotationScript($keys)
{
    $script = '<?php
/**
 * Script tự động chuyển đổi API Key khi hết quota
 */

function rotateApiKey() {
    $keysFile = "api_keys.json";
    $currentKeyFile = "current_key.txt";
    
    if (!file_exists($keysFile)) {
        return false;
    }
    
    $keys = json_decode(file_get_contents($keysFile), true);
    if (empty($keys)) {
        return false;
    }
    
    // Đọc key hiện tại
    $currentIndex = 0;
    if (file_exists($currentKeyFile)) {
        $currentIndex = (int)file_get_contents($currentKeyFile);
    }
    
    // Chuyển sang key tiếp theo
    $nextIndex = ($currentIndex + 1) % count($keys);
    $newKey = $keys[$nextIndex];
    
    // Cập nhật .env
    $envContent = file_get_contents(".env");
    $envContent = preg_replace("/GEMINI_API_KEY=.*/", "GEMINI_API_KEY=" . $newKey, $envContent);
    file_put_contents(".env", $envContent);
    
    // Lưu index hiện tại
    file_put_contents($currentKeyFile, $nextIndex);
    
    // Clear config cache
    exec("php artisan config:clear");
    
    return $newKey;
}

// Sử dụng: $newKey = rotateApiKey();
?>';

    file_put_contents('rotate_api_key.php', $script);
    echo "✅ Đã tạo script rotate_api_key.php\n";
}

function updateEnvWithKey($key)
{
    $envContent = file_get_contents('.env');
    $envContent = preg_replace('/GEMINI_API_KEY=.*/', 'GEMINI_API_KEY=' . $key, $envContent);
    file_put_contents('.env', $envContent);
    exec('php artisan config:clear');
}

echo "\n🎯 Hướng dẫn sử dụng:\n";
echo "1. Khi API Key hết quota, chạy: php rotate_api_key.php\n";
echo "2. Script sẽ tự động chuyển sang key tiếp theo\n";
echo "3. Luân phiên sử dụng tất cả keys\n\n";

echo "🚀 Khởi động server với API Key mới:\n";
echo "php artisan serve\n";

?>