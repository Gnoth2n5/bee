<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 WATCHING LOCATION LOGS\n";
echo "========================\n";
echo "Monitoring Laravel logs for location-related activities...\n";
echo "Press Ctrl+C to stop\n\n";

$logFile = storage_path('logs/laravel.log');
$lastSize = 0;

if (file_exists($logFile)) {
    $lastSize = filesize($logFile);
    echo "📁 Log file: {$logFile}\n";
    echo "📊 Current size: " . number_format($lastSize) . " bytes\n\n";
} else {
    echo "❌ Log file not found: {$logFile}\n";
    exit(1);
}

// Show recent location logs first
echo "📋 RECENT LOCATION LOGS (last 10):\n";
echo "==================================\n";

$content = file_get_contents($logFile);
$lines = explode("\n", $content);
$locationLines = [];

foreach ($lines as $line) {
    if (
        strpos($line, '[WeatherRecipeSection]') !== false ||
        strpos($line, '[WeatherRecipeSuggestions]') !== false ||
        strpos($line, '[ProfilePage]') !== false ||
        strpos($line, 'setUserLocation') !== false ||
        strpos($line, 'Location detection') !== false ||
        strpos($line, 'Session updated') !== false
    ) {
        $locationLines[] = $line;
    }
}

$recentLines = array_slice($locationLines, -10);
foreach ($recentLines as $line) {
    echo "📝 " . substr($line, 0, 150) . "...\n";
}

echo "\n🔄 WATCHING FOR NEW LOGS...\n";
echo "============================\n";

while (true) {
    clearstatcache();
    $currentSize = filesize($logFile);

    if ($currentSize > $lastSize) {
        $newContent = file_get_contents($logFile, false, null, $lastSize);
        $newLines = explode("\n", trim($newContent));

        foreach ($newLines as $line) {
            if (empty($line)) continue;

            // Check if line contains location-related keywords
            if (
                strpos($line, '[WeatherRecipeSection]') !== false ||
                strpos($line, '[WeatherRecipeSuggestions]') !== false ||
                strpos($line, '[ProfilePage]') !== false ||
                strpos($line, 'setUserLocation') !== false ||
                strpos($line, 'Location detection') !== false ||
                strpos($line, 'Session updated') !== false ||
                strpos($line, 'user_location') !== false
            ) {

                // Add timestamp and format
                $timestamp = date('H:i:s');

                // Extract emoji and component from log line
                if (strpos($line, '🎯') !== false) {
                    echo "🎯 [{$timestamp}] " . substr($line, strpos($line, '🎯') + 4) . "\n";
                } elseif (strpos($line, '✅') !== false) {
                    echo "✅ [{$timestamp}] " . substr($line, strpos($line, '✅') + 4) . "\n";
                } elseif (strpos($line, '💾') !== false) {
                    echo "💾 [{$timestamp}] " . substr($line, strpos($line, '💾') + 4) . "\n";
                } elseif (strpos($line, '❌') !== false) {
                    echo "❌ [{$timestamp}] " . substr($line, strpos($line, '❌') + 4) . "\n";
                } elseif (strpos($line, '🚀') !== false) {
                    echo "🚀 [{$timestamp}] " . substr($line, strpos($line, '🚀') + 4) . "\n";
                } elseif (strpos($line, '📝') !== false) {
                    echo "📝 [{$timestamp}] " . substr($line, strpos($line, '📝') + 4) . "\n";
                } else {
                    echo "📋 [{$timestamp}] " . $line . "\n";
                }
            }
        }

        $lastSize = $currentSize;
    }

    usleep(500000); // Sleep 0.5 seconds
}
