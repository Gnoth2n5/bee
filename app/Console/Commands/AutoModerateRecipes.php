<?php

namespace App\Console\Commands;

use App\Services\ModerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoModerateRecipes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:auto-moderate {--dry-run : Chỉ kiểm tra mà không thực hiện thay đổi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động phê duyệt/từ chối các công thức đang chờ duyệt';

    /**
     * Execute the console command.
     */
    public function handle(ModerationService $moderationService)
    {
        $this->info('Bắt đầu tự động phê duyệt công thức...');

        if ($this->option('dry-run')) {
            $this->warn('Chế độ DRY RUN - Không thực hiện thay đổi thực tế');
        }

        try {
            $results = $moderationService->autoModeratePendingRecipes();

            $this->info('Kết quả tự động phê duyệt:');
            $this->table(
                ['Loại', 'Số lượng'],
                [
                    ['Tổng cộng', $results['total']],
                    ['Đã phê duyệt', $results['approved']],
                    ['Đã từ chối', $results['rejected']],
                    ['Đã flag', $results['flagged']],
                    ['Tự động phê duyệt', $results['auto_approved']],
                ]
            );

            if (!empty($results['errors'])) {
                $this->error('Có ' . count($results['errors']) . ' lỗi xảy ra:');
                foreach ($results['errors'] as $error) {
                    $this->error("- Recipe ID {$error['recipe_id']}: {$error['error']}");
                }
            }

            $this->info('Hoàn thành tự động phê duyệt!');
            
            Log::info('Auto moderation command completed', $results);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Lỗi khi thực hiện tự động phê duyệt: ' . $e->getMessage());
            Log::error('Auto moderation command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}