<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckGoogleLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:check-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra logs Google OAuth';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Kiểm tra Google OAuth Logs');
        $this->line('=============================');
        $this->line('');

        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            $this->error('❌ File log không tồn tại!');
            return 1;
        }

        $logContent = file_get_contents($logPath);

        // Tìm các log liên quan đến Google
        $googleLogs = [];
        $lines = explode("\n", $logContent);

        foreach ($lines as $line) {
            if (
                strpos($line, 'google') !== false ||
                strpos($line, 'Google') !== false ||
                strpos($line, 'OAuth') !== false ||
                strpos($line, 'Socialite') !== false
            ) {
                $googleLogs[] = $line;
            }
        }

        if (empty($googleLogs)) {
            $this->info('📝 Không tìm thấy log nào liên quan đến Google OAuth');
            $this->line('');
            $this->info('🔧 Để debug, hãy:');
            $this->line('1. Thử đăng nhập Google lại');
            $this->line('2. Chạy lại command này');
            $this->line('3. Kiểm tra logs mới');
            return 0;
        }

        $this->info('📋 Logs Google OAuth (20 dòng cuối):');
        $this->line('');

        $recentLogs = array_slice($googleLogs, -20);
        foreach ($recentLogs as $log) {
            if (trim($log)) {
                $this->line($log);
            }
        }

        $this->line('');
        $this->info('🔧 Hướng dẫn debug:');
        $this->line('1. Kiểm tra Client Secret đã được cập nhật chưa');
        $this->line('2. Đảm bảo Redirect URI khớp chính xác');
        $this->line('3. Clear cache: php artisan config:clear');
        $this->line('4. Test lại: php artisan serve');

        return 0;
    }
}