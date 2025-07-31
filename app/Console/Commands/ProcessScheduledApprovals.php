<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Services\RecipeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledApprovals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:process-scheduled-approvals {--dry-run : Chạy thử nghiệm không thay đổi dữ liệu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xử lý tự động phê duyệt/từ chối công thức theo lịch trình đã set';

    /**
     * Execute the console command.
     */
    public function handle(RecipeService $recipeService)
    {
        $this->info('⏰ Bắt đầu xử lý phê duyệt/từ chối theo lịch trình...');

        if ($this->option('dry-run')) {
            $this->warn('⚠️  Chế độ thử nghiệm - không thay đổi dữ liệu');
        }

        $startTime = now();

        try {
            // Lấy các công thức có auto_approve_at đã qua thời gian hiện tại
            $recipesToApprove = Recipe::where('status', 'pending')
                ->whereNotNull('auto_approve_at')
                ->where('auto_approve_at', '<=', now())
                ->get();

            // Lấy các công thức có auto_reject_at đã qua thời gian hiện tại
            $recipesToReject = Recipe::where('status', 'pending')
                ->whereNotNull('auto_reject_at')
                ->where('auto_reject_at', '<=', now())
                ->get();

            $this->info("📋 Tìm thấy {$recipesToApprove->count()} công thức cần phê duyệt theo lịch trình");
            $this->info("📋 Tìm thấy {$recipesToReject->count()} công thức cần từ chối theo lịch trình");

            $approvedCount = 0;
            $rejectedCount = 0;
            $errors = [];

            // Xử lý phê duyệt theo lịch trình
            foreach ($recipesToApprove as $recipe) {
                try {
                    if (!$this->option('dry-run')) {
                        $recipeService->approve($recipe, null, 'Tự động phê duyệt theo lịch trình');

                        // Clear auto_approve_at sau khi đã phê duyệt
                        $recipe->update(['auto_approve_at' => null]);
                    }

                    $approvedCount++;
                    $this->line("✅ Đã phê duyệt: {$recipe->title} (ID: {$recipe->id})");

                } catch (\Exception $e) {
                    $error = "Lỗi phê duyệt công thức {$recipe->id}: {$e->getMessage()}";
                    $errors[] = $error;
                    $this->error($error);
                    Log::error($error, ['recipe_id' => $recipe->id, 'error' => $e->getMessage()]);
                }
            }

            // Xử lý từ chối theo lịch trình
            foreach ($recipesToReject as $recipe) {
                try {
                    if (!$this->option('dry-run')) {
                        $recipeService->systemReject($recipe, 'Tự động từ chối theo lịch trình');

                        // Clear auto_reject_at sau khi đã từ chối
                        $recipe->update(['auto_reject_at' => null]);
                    }

                    $rejectedCount++;
                    $this->line("❌ Đã từ chối: {$recipe->title} (ID: {$recipe->id})");

                } catch (\Exception $e) {
                    $error = "Lỗi từ chối công thức {$recipe->id}: {$e->getMessage()}";
                    $errors[] = $error;
                    $this->error($error);
                    Log::error($error, ['recipe_id' => $recipe->id, 'error' => $e->getMessage()]);
                }
            }

            $duration = now()->diffInSeconds($startTime);

            $this->newLine();
            $this->info('📊 Kết quả xử lý phê duyệt/từ chối theo lịch trình:');
            $this->table(
                ['Thống kê', 'Số lượng'],
                [
                    ['Tổng công thức cần phê duyệt', $recipesToApprove->count()],
                    ['✅ Đã phê duyệt thành công', $approvedCount],
                    ['Tổng công thức cần từ chối', $recipesToReject->count()],
                    ['❌ Đã từ chối thành công', $rejectedCount],
                    ['⚠️ Lỗi', count($errors)],
                    ['⏱️  Thời gian xử lý', $duration . ' giây'],
                ]
            );

            if (!empty($errors)) {
                $this->newLine();
                $this->warn('⚠️  Chi tiết lỗi:');
                foreach ($errors as $error) {
                    $this->line("  - {$error}");
                }
            }

            if ($this->option('dry-run')) {
                $this->warn('Đây là kết quả thử nghiệm. Chạy lại không có --dry-run để thực hiện thay đổi.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi xử lý phê duyệt theo lịch trình: ' . $e->getMessage());
            Log::error('ProcessScheduledApprovals error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}