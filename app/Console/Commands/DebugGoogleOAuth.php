<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Socialite\Facades\Socialite;

class DebugGoogleOAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug Google OAuth configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Debug Google OAuth Configuration');
        $this->line('');

        // Kiểm tra file .env
        $this->info('📁 Kiểm tra file .env:');
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $this->line('✅ File .env tồn tại');

            $envContent = file_get_contents($envPath);
            $hasGoogleClientId = strpos($envContent, 'GOOGLE_CLIENT_ID') !== false;
            $hasGoogleClientSecret = strpos($envContent, 'GOOGLE_CLIENT_SECRET') !== false;
            $hasAppUrl = strpos($envContent, 'APP_URL') !== false;

            $this->line('GOOGLE_CLIENT_ID: ' . ($hasGoogleClientId ? '✅ Có trong .env' : '❌ Không có trong .env'));
            $this->line('GOOGLE_CLIENT_SECRET: ' . ($hasGoogleClientSecret ? '✅ Có trong .env' : '❌ Không có trong .env'));
            $this->line('APP_URL: ' . ($hasAppUrl ? '✅ Có trong .env' : '❌ Không có trong .env'));
        } else {
            $this->error('❌ File .env không tồn tại!');
        }

        $this->line('');

        // Kiểm tra config
        $this->info('⚙️ Kiểm tra config:');
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUrl = config('services.google.redirect');
        $appUrl = config('app.url');

        $this->line('APP_URL: ' . ($appUrl ?: '❌ Chưa cấu hình'));
        $this->line('GOOGLE_CLIENT_ID: ' . ($clientId ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'));
        $this->line('GOOGLE_CLIENT_SECRET: ' . ($clientSecret ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'));
        $this->line('Redirect URL: ' . ($redirectUrl ?: '❌ Chưa cấu hình'));

        $this->line('');

        // Kiểm tra routes
        $this->info('🛣️ Kiểm tra routes:');
        $routes = \Route::getRoutes();
        $googleRoutes = [];

        foreach ($routes as $route) {
            if (strpos($route->uri(), 'google') !== false) {
                $googleRoutes[] = $route->uri() . ' -> ' . $route->getName();
            }
        }

        if (!empty($googleRoutes)) {
            foreach ($googleRoutes as $route) {
                $this->line('✅ ' . $route);
            }
        } else {
            $this->error('❌ Không tìm thấy Google routes!');
        }

        $this->line('');

        // Kiểm tra Socialite config
        $this->info('🔧 Kiểm tra Socialite config:');
        try {
            $socialiteConfig = config('services.google');
            if ($socialiteConfig) {
                $this->line('✅ Socialite config tồn tại');
                $this->line('Client ID: ' . ($socialiteConfig['client_id'] ? '✅ Có' : '❌ Không có'));
                $this->line('Client Secret: ' . ($socialiteConfig['client_secret'] ? '✅ Có' : '❌ Không có'));
                $this->line('Redirect: ' . ($socialiteConfig['redirect'] ?: '❌ Không có'));
            } else {
                $this->error('❌ Socialite config không tồn tại!');
            }
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi kiểm tra Socialite config: ' . $e->getMessage());
        }

        $this->line('');

        // Hướng dẫn fix
        if (!$clientId || !$clientSecret) {
            $this->error('❌ VẤN ĐỀ: Thiếu Google OAuth credentials!');
            $this->line('');
            $this->showFixInstructions();
            return 1;
        }

        $this->info('✅ Cấu hình Google OAuth đã hoàn tất!');
        $this->line('');
        $this->info('🧪 Test chức năng:');
        $this->line('1. php artisan serve');
        $this->line('2. Truy cập: http://127.0.0.1:8000/login');
        $this->line('3. Click nút "Đăng nhập bằng Google"');

        return 0;
    }

    private function showFixInstructions()
    {
        $this->info('🔧 CÁCH FIX:');
        $this->line('');

        $this->info('1. 📝 Thêm vào file .env:');
        $this->line('');
        $this->comment('# Google OAuth Configuration');
        $this->comment('GOOGLE_CLIENT_ID=your_client_id_here');
        $this->comment('GOOGLE_CLIENT_SECRET=your_client_secret_here');
        $this->comment('APP_URL=http://127.0.0.1:8000');
        $this->line('');

        $this->info('2. 🔄 Clear cache:');
        $this->line('php artisan config:clear');
        $this->line('php artisan cache:clear');
        $this->line('');

        $this->info('3. ✅ Kiểm tra lại:');
        $this->line('php artisan google:check-config');
        $this->line('');

        $this->warn('⚠️ Lưu ý:');
        $this->line('- Copy chính xác Client ID và Client Secret từ Google Console');
        $this->line('- Không có khoảng trắng xung quanh dấu =');
        $this->line('- Đảm bảo redirect URI khớp chính xác');
    }
}