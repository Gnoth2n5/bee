<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Filament\Resources\UserResource;

echo "=== TEST USER RESOURCE ===\n";

// Test 1: Kiểm tra UserResource có load được không
try {
    $userResource = new UserResource();
    echo "✅ UserResource có thể được khởi tạo\n";
} catch (Exception $e) {
    echo "❌ Lỗi khi khởi tạo UserResource: " . $e->getMessage() . "\n";
}

// Test 2: Kiểm tra routes có tồn tại không
echo "\n=== TEST ROUTES ===\n";

$routes = [
    'filament.admin.resources.users.edit',
    'filament.admin.resources.recipes.index',
    'profile'
];

foreach ($routes as $routeName) {
    try {
        $url = route($routeName, ['record' => 1]);
        echo "✅ Route '{$routeName}' tồn tại: {$url}\n";
    } catch (Exception $e) {
        echo "❌ Route '{$routeName}' không tồn tại: " . $e->getMessage() . "\n";
    }
}

// Test 3: Kiểm tra User model
try {
    $user = User::first();
    if ($user) {
        echo "✅ User model hoạt động bình thường (User ID: {$user->id})\n";
    } else {
        echo "⚠️ Không có user nào trong database\n";
    }
} catch (Exception $e) {
    echo "❌ Lỗi User model: " . $e->getMessage() . "\n";
}

echo "\n=== HOÀN THÀNH TEST ===\n";