<?php

namespace App\Exports;

use App\Models\WeeklyMealPlan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Carbon\Carbon;

class MealPlansZipExport
{
    protected $user;
    protected $mealPlan;
    protected $template;

    public function __construct(User $user = null, WeeklyMealPlan $mealPlan = null, string $template = 'default')
    {
        $this->user = $user;
        $this->mealPlan = $mealPlan;
        $this->template = $template;
    }

    public function export()
    {
        if ($this->mealPlan) {
            return $this->exportSingleMealPlan();
        }

        return $this->exportAllMealPlans();
    }

    private function exportSingleMealPlan()
    {
        $mealPlan = $this->mealPlan;
        $zipFileName = 'ke-hoach-bua-an-' . $mealPlan->week_start->format('Y-m-d') . '-' . now()->format('H-i-s') . '.zip';
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
        $zip->addFromString('README.txt', $this->generateReadme($mealPlan));

        // Thêm file JSON
        $zip->addFromString('meal-plan.json', json_encode($mealPlan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Thêm file CSV
        $csvExport = new MealPlansCsvExport(null, $mealPlan);
        $zip->addFromString('meal-plan.csv', $this->generateCsvContent($csvExport));

        // Thêm file thống kê
        $zip->addFromString('statistics.txt', $this->generateStatistics($mealPlan));

        // Thêm danh sách mua sắm
        $shoppingList = $mealPlan->generateShoppingList();
        $zip->addFromString('shopping-list.txt', $this->generateShoppingListContent($shoppingList));

        // Thêm từng ngày theo template
        $this->addDailyMealsToZip($zip, $mealPlan);

        $zip->close();

        return $zipPath;
    }

    private function exportAllMealPlans()
    {
        $mealPlans = WeeklyMealPlan::where('user_id', $this->user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $zipFileName = 'danh-sach-ke-hoach-bua-an-' . now()->format('Y-m-d_H-i-s') . '.zip';
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
        $zip->addFromString('README.txt', $this->generateAllMealPlansReadme($mealPlans));

        // Thêm file JSON tổng hợp
        $zip->addFromString('meal-plans.json', json_encode($mealPlans, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Thêm file CSV
        $csvExport = new MealPlansCsvExport($this->user);
        $zip->addFromString('meal-plans.csv', $this->generateCsvContent($csvExport));

        // Thêm file thống kê tổng hợp
        $zip->addFromString('statistics.txt', $this->generateAllMealPlansStatistics($mealPlans));

        // Thêm từng meal plan riêng biệt
        foreach ($mealPlans as $mealPlan) {
            $this->addMealPlanToZip($zip, $mealPlan);
        }

        $zip->close();

        return $zipPath;
    }

    private function generateReadme(WeeklyMealPlan $mealPlan): string
    {
        $statistics = $mealPlan->getStatistics();

        return "KẾ HOẠCH BỮA ĂN TUẦN\n" .
            "========================\n\n" .
            "Tên kế hoạch: {$mealPlan->name}\n" .
            "Tuần từ: {$mealPlan->week_start->format('d/m/Y')} đến {$mealPlan->week_start->addDays(6)->format('d/m/Y')}\n" .
            "Trạng thái: " . ($mealPlan->is_active ? 'Hoạt động' : 'Không hoạt động') . "\n\n" .
            "THỐNG KÊ:\n" .
            "- Tổng số bữa ăn: {$statistics['total_meals']}\n" .
            "- Số công thức duy nhất: {$statistics['unique_recipes']}\n" .
            "- % Hoàn thành: {$statistics['completion_percentage']}%\n" .
            "- Tổng calories: " . number_format($mealPlan->total_calories) . "\n" .
            "- Tổng chi phí: " . number_format($mealPlan->total_cost) . " VNĐ\n\n" .
            "CÁC FILE TRONG ZIP:\n" .
            "- meal-plan.json: Dữ liệu JSON đầy đủ\n" .
            "- meal-plan.csv: Dữ liệu CSV\n" .
            "- statistics.txt: Thống kê chi tiết\n" .
            "- shopping-list.txt: Danh sách mua sắm\n" .
            "- daily-meals/: Thư mục chứa từng ngày\n\n" .
            "Tạo lúc: " . now()->format('d/m/Y H:i:s') . "\n";
    }

    private function generateAllMealPlansReadme($mealPlans): string
    {
        $totalPlans = $mealPlans->count();
        $activePlans = $mealPlans->where('is_active', true)->count();

        return "DANH SÁCH KẾ HOẠCH BỮA ĂN\n" .
            "==========================\n\n" .
            "Người dùng: {$this->user->name}\n" .
            "Tổng số kế hoạch: {$totalPlans}\n" .
            "Kế hoạch hoạt động: {$activePlans}\n\n" .
            "CÁC FILE TRONG ZIP:\n" .
            "- meal-plans.json: Dữ liệu JSON tổng hợp\n" .
            "- meal-plans.csv: Dữ liệu CSV\n" .
            "- statistics.txt: Thống kê tổng hợp\n" .
            "- meal-plans/: Thư mục chứa từng kế hoạch\n\n" .
            "Tạo lúc: " . now()->format('d/m/Y H:i:s') . "\n";
    }

    private function generateCsvContent($csvExport): string
    {
        $data = $csvExport->collection();
        $headings = $csvExport->headings();

        $csv = implode(',', $headings) . "\n";

        foreach ($data as $item) {
            $row = $csvExport->map($item);
            if (is_array($row)) {
                $csv .= implode(',', array_map(function ($value) {
                    return '"' . str_replace('"', '""', $value) . '"';
                }, $row)) . "\n";
            }
        }

        return $csv;
    }

    private function generateStatistics(WeeklyMealPlan $mealPlan): string
    {
        $statistics = $mealPlan->getStatistics();

        return "THỐNG KÊ CHI TIẾT\n" .
            "==================\n\n" .
            "Kế hoạch: {$mealPlan->name}\n" .
            "Tuần: {$mealPlan->week_start->format('d/m/Y')} - {$mealPlan->week_start->addDays(6)->format('d/m/Y')}\n\n" .
            "TỔNG QUAN:\n" .
            "- Tổng số bữa ăn: {$statistics['total_meals']}\n" .
            "- Số công thức duy nhất: {$statistics['unique_recipes']}\n" .
            "- % Hoàn thành: {$statistics['completion_percentage']}%\n" .
            "- Tổng calories: " . number_format($mealPlan->total_calories) . "\n" .
            "- Tổng chi phí: " . number_format($mealPlan->total_cost) . " VNĐ\n\n" .
            "TÍNH NĂNG SỬ DỤNG:\n" .
            "- Tối ưu thời tiết: " . ($mealPlan->weather_optimized ? 'Có' : 'Không') . "\n" .
            "- Sử dụng AI: " . ($mealPlan->ai_suggestions_used ? 'Có' : 'Không') . "\n" .
            "- Đã tạo danh sách mua sắm: " . ($mealPlan->shopping_list_generated ? 'Có' : 'Không') . "\n\n" .
            "THỜI GIAN:\n" .
            "- Tạo lúc: {$mealPlan->created_at->format('d/m/Y H:i:s')}\n" .
            "- Cập nhật lúc: {$mealPlan->updated_at->format('d/m/Y H:i:s')}\n";
    }

    private function generateAllMealPlansStatistics($mealPlans): string
    {
        $totalPlans = $mealPlans->count();
        $activePlans = $mealPlans->where('is_active', true)->count();
        $totalCalories = $mealPlans->sum('total_calories');
        $totalCost = $mealPlans->sum('total_cost');

        return "THỐNG KÊ TỔNG HỢP\n" .
            "==================\n\n" .
            "Người dùng: {$this->user->name}\n\n" .
            "TỔNG QUAN:\n" .
            "- Tổng số kế hoạch: {$totalPlans}\n" .
            "- Kế hoạch hoạt động: {$activePlans}\n" .
            "- Tổng calories: " . number_format($totalCalories) . "\n" .
            "- Tổng chi phí: " . number_format($totalCost) . " VNĐ\n\n" .
            "CHI TIẾT TỪNG KẾ HOẠCH:\n";

        foreach ($mealPlans as $mealPlan) {
            $statistics = $mealPlan->getStatistics();
            $content .= "- {$mealPlan->name} ({$mealPlan->week_start->format('d/m/Y')}): " .
                "{$statistics['total_meals']} bữa, " .
                "{$statistics['completion_percentage']}% hoàn thành\n";
        }

        return $content . "\nTạo lúc: " . now()->format('d/m/Y H:i:s') . "\n";
    }

    private function generateShoppingListContent($shoppingList): string
    {
        $content = "DANH SÁCH MUA SẮM\n" .
            "==================\n\n";

        if (empty($shoppingList)) {
            return $content . "Chưa có danh sách mua sắm.\n";
        }

        foreach ($shoppingList as $ingredient => $details) {
            $content .= "- {$ingredient}: {$details['amount']} {$details['unit']}\n";
            if (!empty($details['recipes'])) {
                $content .= "  (Dùng cho: " . implode(', ', $details['recipes']) . ")\n";
            }
            $content .= "\n";
        }

        return $content;
    }

    private function addDailyMealsToZip(ZipArchive $zip, WeeklyMealPlan $mealPlan)
    {
        $days = [
            'monday' => 'Thu-2',
            'tuesday' => 'Thu-3',
            'wednesday' => 'Thu-4',
            'thursday' => 'Thu-5',
            'friday' => 'Thu-6',
            'saturday' => 'Thu-7',
            'sunday' => 'Chu-nhat'
        ];

        $mealTypes = WeeklyMealPlan::getMealTypes();

        foreach ($days as $dayKey => $dayLabel) {
            $dayDate = $mealPlan->week_start->copy()->addDays(array_search($dayKey, array_keys($days)));
            $dayContent = $this->generateDayContent($mealPlan, $dayKey, $dayLabel, $dayDate, $mealTypes);

            $zip->addFromString("daily-meals/{$dayLabel}-{$dayDate->format('Y-m-d')}.txt", $dayContent);
        }
    }

    private function addMealPlanToZip(ZipArchive $zip, WeeklyMealPlan $mealPlan)
    {
        $folderName = 'meal-plans/' . $mealPlan->week_start->format('Y-m-d') . '-' . $mealPlan->id;

        // Thêm file JSON của meal plan
        $zip->addFromString("{$folderName}/meal-plan.json", json_encode($mealPlan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Thêm file thống kê
        $statistics = $mealPlan->getStatistics();
        $zip->addFromString("{$folderName}/statistics.txt", $this->generateStatistics($mealPlan));

        // Thêm danh sách mua sắm
        $shoppingList = $mealPlan->generateShoppingList();
        $zip->addFromString("{$folderName}/shopping-list.txt", $this->generateShoppingListContent($shoppingList));
    }

    private function generateDayContent(WeeklyMealPlan $mealPlan, $dayKey, $dayLabel, $dayDate, $mealTypes): string
    {
        $content = "KẾ HOẠCH BỮA ĂN - {$dayLabel}\n";
        $content .= "Ngày: {$dayDate->format('d/m/Y')}\n";
        $content .= str_repeat("=", 50) . "\n\n";

        foreach ($mealTypes as $mealType => $mealTypeLabel) {
            $content .= "{$mealTypeLabel}:\n";
            $content .= str_repeat("-", 30) . "\n";

            $meals = $mealPlan->getMealsForDay($dayKey, $mealType);

            if (!empty($meals)) {
                foreach ($meals as $recipeId) {
                    $recipe = \App\Models\Recipe::find($recipeId);
                    if ($recipe) {
                        $content .= "- {$recipe->title}\n";
                        $content .= "  Calories: {$recipe->calories_per_serving}\n";
                        $content .= "  Thời gian: {$recipe->cooking_time} phút\n";
                        $content .= "  Độ khó: {$recipe->difficulty}\n";

                        if (!empty($recipe->description)) {
                            $content .= "  Mô tả: {$recipe->description}\n";
                        }
                        $content .= "\n";
                    }
                }
            } else {
                $content .= "- Chưa có món ăn\n\n";
            }
        }

        return $content;
    }
}
