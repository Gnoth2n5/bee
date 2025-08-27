<?php

namespace App\Exports;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Carbon\Carbon;

class RecipesZipExport
{
    protected $user;
    protected $filters;
    protected $template;

    public function __construct(User $user = null, array $filters = [], string $template = 'default')
    {
        $this->user = $user;
        $this->filters = $filters;
        $this->template = $template;
    }

    public function export()
    {
        $recipes = $this->getRecipes();
        $zipFileName = $this->generateZipFileName();
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Tạo thư mục temp nếu chưa có
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            throw new \Exception('Không thể tạo file ZIP');
        }

        // Thêm file README
        $zip->addFromString('README.txt', $this->generateReadme($recipes));

        // Thêm file JSON tổng hợp
        $zip->addFromString('recipes.json', json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Thêm file CSV
        $zip->addFromString('recipes.csv', $this->generateCsv($recipes));

        // Thêm từng công thức theo template
        foreach ($recipes as $recipe) {
            $this->addRecipeToZip($zip, $recipe);
        }

        // Thêm file thống kê
        $zip->addFromString('statistics.txt', $this->generateStatistics($recipes));

        $zip->close();

        return $zipPath;
    }

    private function getRecipes()
    {
        $query = Recipe::with(['user', 'categories', 'tags', 'images']);

        if ($this->user) {
            $query->where('user_id', $this->user->id);
        }

        // Apply filters
        if (!empty($this->filters['category'])) {
            $query->whereHas('categories', function ($q) {
                $q->where('id', $this->filters['category']);
            });
        }

        if (!empty($this->filters['difficulty'])) {
            $query->where('difficulty', $this->filters['difficulty']);
        }

        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    private function generateZipFileName(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $userPrefix = $this->user ? $this->user->name . '_' : '';
        return "recipes_{$userPrefix}{$timestamp}.zip";
    }

    private function generateReadme($recipes): string
    {
        $content = "DANH SÁCH CÔNG THỨC\n";
        $content .= "==================\n\n";
        $content .= "Ngày xuất: " . now()->format('d/m/Y H:i:s') . "\n";
        if ($this->user) {
            $content .= "Người dùng: " . $this->user->name . "\n";
        }
        $content .= "Tổng số công thức: " . $recipes->count() . "\n\n";

        $content .= "Cấu trúc thư mục:\n";
        $content .= "- README.txt: File này\n";
        $content .= "- recipes.json: Dữ liệu JSON tổng hợp\n";
        $content .= "- recipes.csv: Dữ liệu CSV\n";
        $content .= "- statistics.txt: Thống kê chi tiết\n";
        $content .= "- recipes/: Thư mục chứa từng công thức\n\n";

        $content .= "Danh sách công thức:\n";
        foreach ($recipes as $index => $recipe) {
            $content .= sprintf("%d. %s (%s)\n", $index + 1, $recipe->title, $recipe->difficulty);
        }

        return $content;
    }

    private function generateCsv($recipes): string
    {
        $headers = [
            'ID',
            'Tên công thức',
            'Mô tả',
            'Thời gian nấu',
            'Độ khó',
            'Khẩu phần',
            'Calories',
            'Trạng thái',
            'Tác giả',
            'Ngày tạo'
        ];

        $csv = implode(',', $headers) . "\n";

        foreach ($recipes as $recipe) {
            $row = [
                $recipe->id,
                '"' . str_replace('"', '""', $recipe->title) . '"',
                '"' . str_replace('"', '""', $recipe->description ?? '') . '"',
                $recipe->cooking_time ?? '',
                $this->getDifficultyLabel($recipe->difficulty),
                $recipe->servings,
                $recipe->calories_per_serving ?? '',
                $this->getStatusLabel($recipe->status),
                '"' . str_replace('"', '""', $recipe->user ? $recipe->user->name : 'N/A') . '"',
                $recipe->created_at ? $recipe->created_at->format('d/m/Y') : ''
            ];
            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }

    private function addRecipeToZip($zip, $recipe)
    {
        $recipeFolder = 'recipes/' . $this->sanitizeFileName($recipe->title) . '/';

        // Thêm file thông tin chính
        $zip->addFromString($recipeFolder . 'info.txt', $this->generateRecipeInfo($recipe));

        // Thêm file nguyên liệu
        $zip->addFromString($recipeFolder . 'ingredients.txt', $this->generateIngredientsList($recipe));

        // Thêm file hướng dẫn
        $zip->addFromString($recipeFolder . 'instructions.txt', $this->generateInstructions($recipe));

        // Thêm file JSON riêng
        $zip->addFromString($recipeFolder . 'recipe.json', json_encode($recipe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Thêm file HTML nếu template là HTML
        if ($this->template === 'html') {
            $zip->addFromString($recipeFolder . 'recipe.html', $this->generateHtmlTemplate($recipe));
        }

        // Thêm file Markdown nếu template là Markdown
        if ($this->template === 'markdown') {
            $zip->addFromString($recipeFolder . 'recipe.md', $this->generateMarkdownTemplate($recipe));
        }
    }

    private function generateRecipeInfo($recipe): string
    {
        $content = "THÔNG TIN CÔNG THỨC\n";
        $content .= "==================\n\n";
        $content .= "Tên: " . $recipe->title . "\n";
        $content .= "Mô tả: " . ($recipe->description ?? 'Không có mô tả') . "\n";
        $content .= "Tóm tắt: " . ($recipe->summary ?? 'Không có tóm tắt') . "\n\n";

        $content .= "THÔNG TIN NẤU NƯỚNG:\n";
        $content .= "- Thời gian nấu: " . ($recipe->cooking_time ?? 0) . " phút\n";
        $content .= "- Thời gian chuẩn bị: " . ($recipe->preparation_time ?? 0) . " phút\n";
        $content .= "- Tổng thời gian: " . ($recipe->total_time ?? 0) . " phút\n";
        $content .= "- Độ khó: " . $this->getDifficultyLabel($recipe->difficulty) . "\n";
        $content .= "- Khẩu phần: " . $recipe->servings . " người\n";
        $content .= "- Calories/khẩu phần: " . ($recipe->calories_per_serving ?? 'Không có') . "\n\n";

        $content .= "THÔNG TIN KHÁC:\n";
        $content .= "- Trạng thái: " . $this->getStatusLabel($recipe->status) . "\n";
        $content .= "- Tác giả: " . ($recipe->user ? $recipe->user->name : 'N/A') . "\n";
        $content .= "- Ngày tạo: " . $recipe->created_at->format('d/m/Y H:i') . "\n";
        $content .= "- Lượt xem: " . $recipe->view_count . "\n";
        $content .= "- Lượt yêu thích: " . $recipe->favorite_count . "\n";
        $content .= "- Đánh giá trung bình: " . $recipe->average_rating . "/5 (" . $recipe->rating_count . " lượt đánh giá)\n\n";

        if ($recipe->tips) {
            $content .= "MẸO NẤU:\n" . $recipe->tips . "\n\n";
        }

        if ($recipe->notes) {
            $content .= "GHI CHÚ:\n" . $recipe->notes . "\n\n";
        }

        return $content;
    }

    private function generateIngredientsList($recipe): string
    {
        $content = "NGUYÊN LIỆU\n";
        $content .= "============\n\n";
        $content .= "Khẩu phần: " . $recipe->servings . " người\n\n";

        if (!$recipe->ingredients) {
            $content .= "Không có thông tin nguyên liệu.\n";
            return $content;
        }

        $ingredients = is_string($recipe->ingredients) ? json_decode($recipe->ingredients, true) : $recipe->ingredients;

        if (!is_array($ingredients)) {
            $content .= "Không có thông tin nguyên liệu.\n";
            return $content;
        }

        foreach ($ingredients as $index => $ingredient) {
            if (is_array($ingredient)) {
                $name = $ingredient['name'] ?? '';
                $amount = $ingredient['amount'] ?? '';
                $unit = $ingredient['unit'] ?? '';

                if ($name) {
                    $content .= sprintf("%d. %s: %s %s\n", $index + 1, $name, $amount, $unit);
                }
            }
        }

        return $content;
    }

    private function generateInstructions($recipe): string
    {
        $content = "HƯỚNG DẪN NẤU\n";
        $content .= "==============\n\n";

        if (!$recipe->instructions) {
            $content .= "Không có hướng dẫn nấu.\n";
            return $content;
        }

        $instructions = is_string($recipe->instructions) ? json_decode($recipe->instructions, true) : $recipe->instructions;

        if (!is_array($instructions)) {
            $content .= $recipe->instructions . "\n";
            return $content;
        }

        foreach ($instructions as $index => $instruction) {
            if (is_array($instruction)) {
                $step = $instruction['step'] ?? '';
                if ($step) {
                    $content .= sprintf("Bước %d: %s\n\n", $index + 1, $step);
                }
            } else {
                $content .= sprintf("Bước %d: %s\n\n", $index + 1, $instruction);
            }
        }

        return $content;
    }

    private function generateHtmlTemplate($recipe): string
    {
        $html = '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($recipe->title) . '</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #FF6B35; padding-bottom: 20px; margin-bottom: 30px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .info-card { background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; }
        .section { margin: 30px 0; }
        .section h2 { color: #FF6B35; border-bottom: 1px solid #FF6B35; padding-bottom: 10px; }
        .ingredient-item { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; }
        .instruction-step { margin: 15px 0; padding: 15px; background: #fff3cd; border-left: 4px solid #FF6B35; }
        .tips { background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . htmlspecialchars($recipe->title) . '</h1>
        <p>' . htmlspecialchars($recipe->description ?? '') . '</p>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <h3>Thời gian nấu</h3>
            <p>' . ($recipe->cooking_time ?? 0) . ' phút</p>
        </div>
        <div class="info-card">
            <h3>Độ khó</h3>
            <p>' . $this->getDifficultyLabel($recipe->difficulty) . '</p>
        </div>
        <div class="info-card">
            <h3>Khẩu phần</h3>
            <p>' . $recipe->servings . ' người</p>
        </div>
        <div class="info-card">
            <h3>Calories</h3>
            <p>' . ($recipe->calories_per_serving ?? 'N/A') . '</p>
        </div>
    </div>

    <div class="section">
        <h2>Nguyên liệu</h2>';

        if ($recipe->ingredients) {
            $ingredients = is_string($recipe->ingredients) ? json_decode($recipe->ingredients, true) : $recipe->ingredients;
            if (is_array($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    if (is_array($ingredient)) {
                        $name = $ingredient['name'] ?? '';
                        $amount = $ingredient['amount'] ?? '';
                        $unit = $ingredient['unit'] ?? '';
                        if ($name) {
                            $html .= '<div class="ingredient-item">' . htmlspecialchars($name) . ': ' . htmlspecialchars($amount) . ' ' . htmlspecialchars($unit) . '</div>';
                        }
                    }
                }
            }
        }

        $html .= '</div>

    <div class="section">
        <h2>Hướng dẫn nấu</h2>';

        if ($recipe->instructions) {
            $instructions = is_string($recipe->instructions) ? json_decode($recipe->instructions, true) : $recipe->instructions;
            if (is_array($instructions)) {
                foreach ($instructions as $index => $instruction) {
                    if (is_array($instruction)) {
                        $step = $instruction['step'] ?? '';
                        if ($step) {
                            $html .= '<div class="instruction-step"><strong>Bước ' . ($index + 1) . ':</strong> ' . htmlspecialchars($step) . '</div>';
                        }
                    } else {
                        $html .= '<div class="instruction-step"><strong>Bước ' . ($index + 1) . ':</strong> ' . htmlspecialchars($instruction) . '</div>';
                    }
                }
            }
        }

        $html .= '</div>';

        if ($recipe->tips) {
            $html .= '<div class="tips">
                <h2>Mẹo nấu</h2>
                <p>' . htmlspecialchars($recipe->tips) . '</p>
            </div>';
        }

        $html .= '</body>
</html>';

        return $html;
    }

    private function generateMarkdownTemplate($recipe): string
    {
        $content = "# " . $recipe->title . "\n\n";

        if ($recipe->description) {
            $content .= $recipe->description . "\n\n";
        }

        $content .= "## Thông tin cơ bản\n\n";
        $content .= "- **Thời gian nấu:** " . ($recipe->cooking_time ?? 0) . " phút\n";
        $content .= "- **Thời gian chuẩn bị:** " . ($recipe->preparation_time ?? 0) . " phút\n";
        $content .= "- **Tổng thời gian:** " . ($recipe->total_time ?? 0) . " phút\n";
        $content .= "- **Độ khó:** " . $this->getDifficultyLabel($recipe->difficulty) . "\n";
        $content .= "- **Khẩu phần:** " . $recipe->servings . " người\n";
        $content .= "- **Calories/khẩu phần:** " . ($recipe->calories_per_serving ?? 'N/A') . "\n\n";

        $content .= "## Nguyên liệu\n\n";

        if ($recipe->ingredients) {
            $ingredients = is_string($recipe->ingredients) ? json_decode($recipe->ingredients, true) : $recipe->ingredients;
            if (is_array($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    if (is_array($ingredient)) {
                        $name = $ingredient['name'] ?? '';
                        $amount = $ingredient['amount'] ?? '';
                        $unit = $ingredient['unit'] ?? '';
                        if ($name) {
                            $content .= "- " . $name . ": " . $amount . " " . $unit . "\n";
                        }
                    }
                }
            }
        }

        $content .= "\n## Hướng dẫn nấu\n\n";

        if ($recipe->instructions) {
            $instructions = is_string($recipe->instructions) ? json_decode($recipe->instructions, true) : $recipe->instructions;
            if (is_array($instructions)) {
                foreach ($instructions as $index => $instruction) {
                    if (is_array($instruction)) {
                        $step = $instruction['step'] ?? '';
                        if ($step) {
                            $content .= ($index + 1) . ". " . $step . "\n\n";
                        }
                    } else {
                        $content .= ($index + 1) . ". " . $instruction . "\n\n";
                    }
                }
            }
        }

        if ($recipe->tips) {
            $content .= "## Mẹo nấu\n\n";
            $content .= $recipe->tips . "\n\n";
        }

        if ($recipe->notes) {
            $content .= "## Ghi chú\n\n";
            $content .= $recipe->notes . "\n\n";
        }

        $content .= "---\n";
        $content .= "*Tác giả: " . ($recipe->user ? $recipe->user->name : 'N/A') . "*\n";
        $content .= "*Ngày tạo: " . $recipe->created_at->format('d/m/Y') . "*\n";

        return $content;
    }

    private function generateStatistics($recipes): string
    {
        $content = "THỐNG KÊ CÔNG THỨC\n";
        $content .= "==================\n\n";

        $content .= "Tổng số công thức: " . $recipes->count() . "\n\n";

        // Thống kê theo độ khó
        $difficultyStats = $recipes->groupBy('difficulty')->map->count();
        $content .= "Theo độ khó:\n";
        foreach ($difficultyStats as $difficulty => $count) {
            $content .= "- " . $this->getDifficultyLabel($difficulty) . ": " . $count . " công thức\n";
        }
        $content .= "\n";

        // Thống kê theo trạng thái
        $statusStats = $recipes->groupBy('status')->map->count();
        $content .= "Theo trạng thái:\n";
        foreach ($statusStats as $status => $count) {
            $content .= "- " . $this->getStatusLabel($status) . ": " . $count . " công thức\n";
        }
        $content .= "\n";

        // Thống kê thời gian
        $avgCookingTime = $recipes->whereNotNull('cooking_time')->avg('cooking_time');
        $content .= "Thời gian nấu trung bình: " . round($avgCookingTime, 1) . " phút\n";

        $avgRating = $recipes->where('average_rating', '>', 0)->avg('average_rating');
        $content .= "Đánh giá trung bình: " . round($avgRating, 2) . "/5\n";

        $totalViews = $recipes->sum('view_count');
        $content .= "Tổng lượt xem: " . number_format($totalViews) . "\n";

        $totalFavorites = $recipes->sum('favorite_count');
        $content .= "Tổng lượt yêu thích: " . number_format($totalFavorites) . "\n\n";

        // Top 5 công thức được xem nhiều nhất
        $topViewed = $recipes->sortByDesc('view_count')->take(5);
        $content .= "Top 5 công thức được xem nhiều nhất:\n";
        foreach ($topViewed as $index => $recipe) {
            $content .= ($index + 1) . ". " . $recipe->title . " (" . $recipe->view_count . " lượt xem)\n";
        }
        $content .= "\n";

        // Top 5 công thức được đánh giá cao nhất
        $topRated = $recipes->where('average_rating', '>', 0)->sortByDesc('average_rating')->take(5);
        $content .= "Top 5 công thức được đánh giá cao nhất:\n";
        foreach ($topRated as $index => $recipe) {
            $content .= ($index + 1) . ". " . $recipe->title . " (" . $recipe->average_rating . "/5)\n";
        }

        return $content;
    }

    private function sanitizeFileName($fileName): string
    {
        // Loại bỏ các ký tự không hợp lệ cho tên file
        $fileName = preg_replace('/[^a-zA-Z0-9\s\-_\.]/', '', $fileName);
        $fileName = str_replace(' ', '_', $fileName);
        return $fileName;
    }

    private function getDifficultyLabel($difficulty): string
    {
        return match ($difficulty) {
            'easy' => 'Dễ',
            'medium' => 'Trung bình',
            'hard' => 'Khó',
            default => $difficulty
        };
    }

    private function getStatusLabel($status): string
    {
        return match ($status) {
            'draft' => 'Bản nháp',
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Bị từ chối',
            default => $status
        };
    }
}

