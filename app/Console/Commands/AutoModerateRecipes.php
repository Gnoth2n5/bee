<?php

namespace App\Console\Commands;

use App\Services\ModerationService;
use Illuminate\Console\Command;

class AutoModerateRecipes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:auto-moderate {--dry-run : Chạy thử nghiệm không thay đổi dữ liệu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động kiểm duyệt các công thức đang chờ phê duyệt';

    /**
     * Execute the console command.
     */
    public function handle(ModerationService $moderationService)
    {
        $this->info('🚀 Bắt đầu kiểm duyệt tự động công thức...');

        if ($this->option('dry-run')) {
            $this->warn('⚠️  Chế độ thử nghiệm - không thay đổi dữ liệu');
        }

        $startTime = now();

        try {
            $results = $moderationService->autoModeratePendingRecipes();

            $this->displayResults($results, $startTime);

            if ($this->option('dry-run')) {
                $this->warn('Đây là kết quả thử nghiệm. Chạy lại không có --dry-run để thực hiện thay đổi.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi kiểm duyệt: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display moderation results.
     */
    protected function displayResults(array $results, $startTime)
    {
        $duration = now()->diffInSeconds($startTime);

        $this->newLine();
        $this->info('📊 Kết quả kiểm duyệt:');
        $this->table(
            ['Thống kê', 'Số lượng'],
            [
                ['Tổng công thức kiểm tra', $results['total']],
                ['✅ Đã phê duyệt', $results['approved']],
                ['⏰ Tự động phê duyệt theo lịch', $results['auto_approved'] ?? 0],
                ['❌ Đã từ chối', $results['rejected']],
                ['🚩 Đã đánh dấu', $results['flagged']],
                ['⏱️  Thời gian xử lý', $duration . ' giây'],
            ]
        );

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->warn('⚠️  Có ' . count($results['errors']) . ' lỗi xảy ra:');

            foreach ($results['errors'] as $error) {
                $this->line("  - Công thức ID {$error['recipe_id']}: {$error['error']}");
            }
        }

        if ($results['total'] > 0) {
            $successRate = round((($results['approved'] + $results['rejected'] + $results['flagged']) / $results['total']) * 100, 2);
            $this->newLine();
            $this->info("🎯 Tỷ lệ xử lý thành công: {$successRate}%");
        }
    }
}