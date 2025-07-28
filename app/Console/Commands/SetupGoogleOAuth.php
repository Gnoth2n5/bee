<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupGoogleOAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hướng dẫn setup Google OAuth từng bước';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Hướng dẫn setup Google OAuth cho BeeFood');
        $this->line('');

        // Kiểm tra cấu hình hiện tại
        $this->info('📋 Kiểm tra cấu hình hiện tại:');
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUrl = config('services.google.redirect');
        $appUrl = config('app.url');

        $this->line('APP_URL: ' . ($appUrl ?: '❌ Chưa cấu hình'));
        $this->line('GOOGLE_CLIENT_ID: ' . ($clientId ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'));
        $this->line('GOOGLE_CLIENT_SECRET: ' . ($clientSecret ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'));
        $this->line('Redirect URL: ' . ($redirectUrl ?: '❌ Chưa cấu hình'));

        $this->line('');

        if (!$clientId || !$clientSecret) {
            $this->error('❌ Thiếu Google OAuth credentials!');
            $this->line('');
            $this->showSetupInstructions();
            return 1;
        }

        $this->info('✅ Cấu hình Google OAuth đã hoàn tất!');
        $this->line('');
        $this->info('🔗 Redirect URL cần thêm vào Google Console:');
        $this->line($redirectUrl);
        $this->line('');
        $this->info('🧪 Test chức năng:');
        $this->line('1. php artisan serve');
        $this->line('2. Truy cập: http://127.0.0.1:8000/login');
        $this->line('3. Click nút "Đăng nhập bằng Google"');

        return 0;
    }

    private function showSetupInstructions()
    {
        $this->info('📝 HƯỚNG DẪN SETUP GOOGLE OAUTH:');
        $this->line('');

        $this->info('🔗 Bước 1: Truy cập Google Cloud Console');
        $this->line('https://console.cloud.google.com/');
        $this->line('');

        $this->info('🏗️ Bước 2: Tạo hoặc chọn project');
        $this->line('- Tạo project mới hoặc chọn project hiện có');
        $this->line('- Đảm bảo project đã được chọn');
        $this->line('');

        $this->info('⚙️ Bước 3: Kích hoạt Google+ API');
        $this->line('- Vào "APIs & Services" > "Library"');
        $this->line('- Tìm "Google+ API" hoặc "Google Identity"');
        $this->line('- Click "Enable"');
        $this->line('');

        $this->info('🔑 Bước 4: Tạo OAuth 2.0 Client ID');
        $this->line('- Vào "APIs & Services" > "Credentials"');
        $this->line('- Click "Create Credentials" > "OAuth 2.0 Client IDs"');
        $this->line('- Chọn "Web application"');
        $this->line('- Đặt tên: "BeeFood Web Client"');
        $this->line('');

        $this->info('🌐 Bước 5: Cấu hình Authorized Redirect URIs');
        $this->line('Thêm chính xác URL này:');
        $this->line('http://127.0.0.1:8000/auth/google/callback');
        $this->line('');

        $this->info('📋 Bước 6: Copy thông tin credentials');
        $this->line('- Copy Client ID và Client Secret');
        $this->line('- Thêm vào file .env:');
        $this->line('');

        $this->comment('GOOGLE_CLIENT_ID=your_client_id_here');
        $this->comment('GOOGLE_CLIENT_SECRET=your_client_secret_here');
        $this->comment('APP_URL=http://127.0.0.1:8000');
        $this->line('');

        $this->info('🔄 Bước 7: Clear cache và test');
        $this->line('php artisan config:clear');
        $this->line('php artisan cache:clear');
        $this->line('php artisan google:check-config');
        $this->line('');

        $this->warn('⚠️ Lưu ý quan trọng:');
        $this->line('- Đảm bảo redirect URI khớp chính xác');
        $this->line('- Không có dấu / thừa ở cuối URL');
        $this->line('- Copy chính xác Client ID và Client Secret');
        $this->line('');

        $this->info('📞 Nếu gặp vấn đề:');
        $this->line('- Kiểm tra logs: storage/logs/laravel.log');
        $this->line('- Chạy: php artisan google:check-config');
        $this->line('- Verify Google Console configuration');
    }
}