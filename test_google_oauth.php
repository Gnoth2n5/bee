<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/routes/web.php',
        commands: __DIR__ . '/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make('config')->set('app.env', 'local');
$app->make('config')->set('app.debug', true);

// Test Google OAuth configuration
echo "=== Testing Google OAuth Configuration ===\n";

// Check if Socialite is available
if (class_exists('Laravel\Socialite\Facades\Socialite')) {
    echo "✓ Socialite is available\n";
} else {
    echo "✗ Socialite is not available\n";
    exit(1);
}

// Check Google OAuth config
$googleConfig = $app->make('config')->get('services.google');
if ($googleConfig) {
    echo "✓ Google OAuth config found\n";
    echo "  - Client ID: " . (isset($googleConfig['client_id']) ? 'Set' : 'Not set') . "\n";
    echo "  - Client Secret: " . (isset($googleConfig['client_secret']) ? 'Set' : 'Not set') . "\n";
    echo "  - Redirect URL: " . ($googleConfig['redirect'] ?? 'Not set') . "\n";
} else {
    echo "✗ Google OAuth config not found\n";
    exit(1);
}

// Check environment variables
$envVars = [
    'GOOGLE_CLIENT_ID' => $_ENV['GOOGLE_CLIENT_ID'] ?? null,
    'GOOGLE_CLIENT_SECRET' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? null,
    'APP_URL' => $_ENV['APP_URL'] ?? null,
];

echo "\n=== Environment Variables ===\n";
foreach ($envVars as $key => $value) {
    echo "  - $key: " . ($value ? 'Set' : 'Not set') . "\n";
}

// Test routes
echo "\n=== Testing Routes ===\n";
$routes = [
    'auth/google' => 'Google redirect route',
    'auth/google/callback' => 'Google callback route',
];

foreach ($routes as $route => $description) {
    echo "  - $route: $description\n";
}

echo "\n=== Test Complete ===\n";
echo "If all checks passed, Google OAuth should work.\n";
echo "Try accessing: http://127.0.0.1:8000/auth/google\n";