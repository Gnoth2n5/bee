<?php

/**
 * Script tự động tìm API key hoạt động
 * Test nhiều API key để tìm key có quota
 */

echo "🔍 Tự động tìm API key hoạt động...\n\n";

// Danh sách API keys để test
$testKeys = [
    'AIzaSyCZw_3yREcmzJF_ScER9mGj9QwGoVNDYM0',
    'AIzaSyAhtJrd-c_5AfVGmv_nfWBK1_vhTbbNVdA',
    'AIzaSyA_iGyoZoGOLkOVh_dH7E9nsnv6_jYfEC8',
    'AIzaSyBfRTdnfja_6s3jVH54Y3m-eCkIWgjoVSU',
    'AIzaSyCW4QoWvpDWtHhR5Yr1EDdla-plR__AmrM',
    'AIzaSyCVLOVxmTyVUI1YiCWWMcvjZA3w23AysHo',
    'AIzaSyBxDDfR6EVVidKdRz8rgBmdiEPkNKF9YNM'
];

echo "📋 Danh sách API keys để test: " . count($testKeys) . " keys\n\n";

$workingKey = null;
$testResults = [];

foreach ($testKeys as $index => $key) {
    echo "🧪 Test API Key " . ($index + 1) . "/" . count($testKeys) . "... ";

    // Test API key
    $result = testApiKey($key);
    $testResults[] = [
        'key' => $key,
        'status' => $result['status'],
        'message' => $result['message']
    ];

    if ($result['status'] === 'working') {
        echo "✅ HOẠT ĐỘNG!\n";
        $workingKey = $key;
        break;
    } else {
        echo "❌ " . $result['message'] . "\n";
    }
}

echo "\n📊 Kết quả test:\n";
foreach ($testResults as $index => $result) {
    $status = $result['status'] === 'working' ? '✅' : '❌';
    echo ($index + 1) . ". {$status} " . substr($result['key'], 0, 20) . "... - " . $result['message'] . "\n";
}

if ($workingKey) {
    echo "\n🎉 Tìm thấy API key hoạt động!\n";
    echo "🔑 Key: " . substr($workingKey, 0, 20) . "...\n";

    // Cập nhật vào .env
    updateEnvWithKey($workingKey);
    echo "✅ Đã cập nhật API key vào .env\n";

    // Khởi động server
    echo "🚀 Khởi động server...\n";
    exec('php artisan serve', $output, $returnCode);

    if ($returnCode === 0) {
        echo "✅ Server đã khởi động thành công!\n";
        echo "🌐 Truy cập: http://localhost:8000\n";
    }
} else {
    echo "\n❌ Không tìm thấy API key hoạt động nào!\n";
    echo "💡 Giải pháp:\n";
    echo "1. Tạo tài khoản Google mới\n";
    echo "2. Tạo API key mới tại: https://aistudio.google.com/app/apikey\n";
    echo "3. Chạy script: php create_gemini_key.php\n";
    echo "4. Hoặc chờ 24h để reset quota\n";
}

function testApiKey($apiKey)
{
    // Tạo file test tạm thời
    $testFile = 'temp_test.php';
    $testCode = '<?php
$apiKey = "' . $apiKey . '";
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

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($httpCode === 200 && isset($result["candidates"][0]["content"]["parts"][0]["text"])) {
    echo "working";
} elseif ($httpCode === 429) {
    echo "quota_exceeded";
} elseif ($httpCode === 400 && strpos($response, "expired") !== false) {
    echo "expired";
} else {
    echo "error:" . $httpCode;
}
?>';

    file_put_contents($testFile, $testCode);

    // Chạy test
    $output = shell_exec('php ' . $testFile . ' 2>&1');
    unlink($testFile); // Xóa file tạm

    if (strpos($output, 'working') !== false) {
        return ['status' => 'working', 'message' => 'Hoạt động tốt'];
    } elseif (strpos($output, 'quota_exceeded') !== false) {
        return ['status' => 'quota_exceeded', 'message' => 'Hết quota'];
    } elseif (strpos($output, 'expired') !== false) {
        return ['status' => 'expired', 'message' => 'Đã hết hạn'];
    } else {
        return ['status' => 'error', 'message' => 'Lỗi: ' . trim($output)];
    }
}

function updateEnvWithKey($key)
{
    $envContent = file_get_contents('.env');
    $envContent = preg_replace('/GEMINI_API_KEY=.*/', 'GEMINI_API_KEY=' . $key, $envContent);
    file_put_contents('.env', $envContent);
    exec('php artisan config:clear');
}

echo "\n🎯 Hướng dẫn tiếp theo:\n";
echo "1. Nếu tìm thấy key hoạt động: Tính năng sẽ hoạt động bình thường\n";
echo "2. Nếu không tìm thấy: Tạo tài khoản Google mới và API key mới\n";
echo "3. Chạy: php create_gemini_key.php để tạo key mới\n";
echo "4. Hoặc chạy: php auto_create_keys.php để tạo nhiều keys\n";

?>