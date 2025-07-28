<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckGoogleOAuthConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:check-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra cấu hình Google OAuth';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Kiểm tra cấu hình Google OAuth...');

        // Kiểm tra các biến môi trường
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUrl = config('services.google.redirect');
        $appUrl = config('app.url');

        $this->line('');
        $this->info('📋 Thông tin cấu hình:');
        $this->line('APP_URL: ' . ($appUrl ?: '❌ Chưa cấu hình'));
        $this->line('GOOGLE_CLIENT_ID: ' . ($clientId ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'));
        $this->line('GOOGLE_CLIENT_SECRET: ' . ($clientSecret ? '✅ Đã cấu hình' : '❌ Chưa cấu hình'));
        $this->line('Redirect URL: ' . ($redirectUrl ?: '❌ Chưa cấu hình'));

        $this->line('');

        if (!$clientId || !$clientSecret) {
            $this->error('❌ Thiếu thông tin Google OAuth credentials!');
            $this->line('');
            $this->info('📝 Hướng dẫn cấu hình:');
            $this->line('1. Truy cập Google Cloud Console: https://console.cloud.google.com/');
            $this->line('2. Tạo OAuth 2.0 Client ID');
            $this->line('3. Thêm vào file .env:');
            $this->line('   GOOGLE_CLIENT_ID=your_client_id');
            $this->line('   GOOGLE_CLIENT_SECRET=your_client_secret');
            $this->line('4. Đảm bảo APP_URL được cấu hình đúng');
            return 1;
        }

        if (!$redirectUrl) {
            $this->error('❌ Redirect URL chưa được cấu hình!');
            $this->line('Hãy đảm bảo APP_URL được cấu hình trong file .env');
            return 1;
        }

        $this->info('✅ Cấu hình Google OAuth đã sẵn sàng!');
        $this->line('');
        $this->info('🔗 Redirect URL cần thêm vào Google Console:');
        $this->line($redirectUrl);
        $this->line('');
        $this->info('📝 Lưu ý:');
        $this->line('- Đảm bảo redirect URL trên Google Console khớp với URL trên');
        $this->line('- Trong môi trường production, sử dụng HTTPS');
        $this->line('- Kiểm tra logs nếu gặp lỗi: storage/logs/laravel.log');

        return 0;
    }
}
