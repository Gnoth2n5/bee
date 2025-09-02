<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AssignRecipeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipe:assign-images {--limit=50 : Number of recipes to assign images to} {--check : Check current image status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign random images from storage/app/public/recipes to recipes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('check')) {
            return $this->checkImageStatus();
        }

        $limit = (int) $this->option('limit');

        // Lấy danh sách ảnh có sẵn
        $imagePath = storage_path('app/public/recipes');
        $images = File::files($imagePath);

        if (empty($images)) {
            $this->error('Không tìm thấy ảnh nào trong thư mục recipes');
            return 1;
        }

        $this->info("Tìm thấy " . count($images) . " ảnh");

        // Lấy recipes chưa có ảnh
        $recipes = Recipe::whereNull('featured_image')
            ->orWhere('featured_image', '')
            ->limit($limit)
            ->get();

        if ($recipes->isEmpty()) {
            $this->info('Tất cả recipes đã có ảnh hoặc không có recipes nào');
            return 0;
        }

        $this->info("Sẽ gắn ảnh cho " . $recipes->count() . " recipes");

        $bar = $this->output->createProgressBar($recipes->count());
        $assignedCount = 0;

        foreach ($recipes as $recipe) {
            // Chọn ảnh ngẫu nhiên
            $randomImage = $images[array_rand($images)];
            $imageName = $randomImage->getFilename();

            // Gắn ảnh vào recipe
            $recipe->featured_image = 'recipes/' . $imageName;
            $recipe->save();

            $assignedCount++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Đã gắn ảnh cho {$assignedCount} recipes thành công!");

        return 0;
    }

    protected function checkImageStatus()
    {
        $totalRecipes = Recipe::count();
        $recipesWithImages = Recipe::whereNotNull('featured_image')
            ->where('featured_image', '!=', '')
            ->count();
        $recipesWithoutImages = $totalRecipes - $recipesWithImages;

        $this->info("=== TRẠNG THÁI ẢNH RECIPES ===");
        $this->info("Tổng số recipes: {$totalRecipes}");
        $this->info("Recipes có ảnh: {$recipesWithImages}");
        $this->info("Recipes chưa có ảnh: {$recipesWithoutImages}");

        if ($recipesWithImages > 0) {
            $this->newLine();
            $this->info("=== 3 RECIPES CÓ ẢNH MẪU ===");
            $sampleRecipes = Recipe::whereNotNull('featured_image')
                ->limit(3)
                ->get(['title', 'featured_image']);

            foreach ($sampleRecipes as $recipe) {
                $this->line("• {$recipe->title}: {$recipe->featured_image}");
            }
        }

        return 0;
    }
}
