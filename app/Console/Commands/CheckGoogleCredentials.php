<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckGoogleCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:check-credentials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra Google OAuth credentials chi tiết';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Kiểm tra Google OAuth Credentials');
        $this->line('=====================================');
        $this->line('');

        // Đọc file .env
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            $this->error('❌ File .env không tồn tại!');
            return 1;
        }

        $envContent = file_get_contents($envPath);

        // Tìm Google credentials
        preg_match('/GOOGLE_CLIENT_ID=(.*)/', $envContent, $clientIdMatches);
        preg_match('/GOOGLE_CLIENT_SECRET=(.*)/', $envContent, $clientSecretMatches);

        $clientId = trim($clientIdMatches[1] ?? '');
        $clientSecret = trim($clientSecretMatches[1] ?? '');

        $this->info('📋 Thông tin hiện tại:');
        $this->line('Client ID: ' . ($clientId ?: '❌ Không tìm thấy'));
        $this->line('Client Secret: ' . ($clientSecret ?: '❌ Không tìm thấy'));
        $this->line('');

        // Kiểm tra xem có phải placeholder không
        if ($clientId === 'your_client_id_here' || $clientSecret === 'your_client_secret_here') {
            $this->error('❌ VẤN ĐỀ: Bạn vẫn đang sử dụng placeholder!');
            $this->line('Hãy thay thế bằng thông tin thực từ Google Console.');
            $this->line('');

            $this->showFixInstructions();
            return 1;
        }

        // Kiểm tra format của Client ID
        if (!preg_match('/^\d+-\w+\.apps\.googleusercontent\.com$/', $clientId)) {
            $this->warn('⚠️ CẢNH BÁO: Client ID có vẻ không đúng format!');
            $this->line('Client ID thường có format: 123456789-abcdefghijklmnop.apps.googleusercontent.com');
            $this->line('');
        }

        // Kiểm tra format của Client Secret
        if (!preg_match('/^GOCSPX-/', $clientSecret)) {
            $this->warn('⚠️ CẢNH BÁO: Client Secret có vẻ không đúng format!');
            $this->line('Client Secret thường bắt đầu bằng: GOCSPX-');
            $this->line('');
        }

        $this->info('✅ Credentials đã được cấu hình!');
        $this->line('');
        $this->info('🧪 Test chức năng:');
        $this->line('1. php artisan serve');
        $this->line('2. Truy cập: http://127.0.0.1:8000/login');
        $this->line('3. Click nút "Đăng nhập bằng Google"');
        $this->line('');

        $this->info('📞 Nếu vẫn gặp lỗi:');
        $this->line('- Kiểm tra Google Console: Redirect URI phải là http://127.0.0.1:8000/auth/google/callback');
        $this->line('- Đảm bảo OAuth 2.0 Client ID đã được tạo đúng');
        $this->line('- Kiểm tra logs: storage/logs/laravel.log');

        return 0;
    }

    private function showFixInstructions()
    {
        $this->info('🔧 CÁCH FIX:');
        $this->line('');

        $this->info('1. Truy cập Google Cloud Console:');
        $this->line('   https://console.cloud.google.com/');
        $this->line('');

        $this->info('2. Tạo OAuth 2.0 Client ID:');
        $this->line('   - Vào "APIs & Services" > "Credentials"');
        $this->line('   - Click "Create Credentials" > "OAuth 2.0 Client IDs"');
        $this->line('   - Chọn "Web application"');
        $this->line('   - Đặt tên: "BeeFood Web Client"');
        $this->line('');

        $this->info('3. Cấu hình Authorized Redirect URIs:');
        $this->line('   Thêm: http://127.0.0.1:8000/auth/google/callback');
        $this->line('');

        $this->info('4. Copy thông tin credentials');
        $this->line('');

        $this->info('5. Cập nhật file .env:');
        $this->line('   Thay thế:');
        $this->comment('   GOOGLE_CLIENT_ID=your_client_id_here');
        $this->comment('   GOOGLE_CLIENT_SECRET=your_client_secret_here');
        $this->line('');

        $this->line('   Thành:');
        $this->comment('   GOOGLE_CLIENT_ID=123456789-abcdefghijklmnop.apps.googleusercontent.com');
        $this->comment('   GOOGLE_CLIENT_SECRET=GOCSPX-abcdefghijklmnopqrstuvwxyz');
        $this->line('');

        $this->info('6. Clear cache:');
        $this->line('   php artisan config:clear');
        $this->line('   php artisan cache:clear');
        $this->line('');

        $this->info('7. Test lại:');
        $this->line('   php artisan google:check-config');
    }
}