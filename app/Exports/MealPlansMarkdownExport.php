<?php

namespace App\Exports;

use App\Models\WeeklyMealPlan;
use App\Models\User;
use Carbon\Carbon;

class MealPlansMarkdownExport
{
    protected $user;
    protected $mealPlan;

    public function __construct(User $user = null, WeeklyMealPlan $mealPlan = null)
    {
        $this->user = $user;
        $this->mealPlan = $mealPlan;
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
        $content = '';

        // Header
        $content .= "# Kế hoạch Bữa ăn: {$mealPlan->name}\n\n";
        $content .= "**Tuần:** {$mealPlan->week_start->format('d/m/Y')} - {$mealPlan->week_end->format('d/m/Y')}\n";
        $content .= "**Trạng thái:** " . ($mealPlan->is_active ? 'Hoạt động' : 'Không hoạt động') . "\n";
        $content .= "**Ngày tạo:** {$mealPlan->created_at->format('d/m/Y H:i')}\n\n";

        // Thống kê tổng quan
        $statistics = $mealPlan->getStatistics();
        $content .= "## 📊 Thống kê Tổng quan\n\n";
        $content .= "| Chỉ số | Giá trị |\n";
        $content .= "|--------|---------|\n";
        $content .= "| Tổng số bữa ăn | {$statistics['total_meals']} |\n";
        $content .= "| Số công thức duy nhất | {$statistics['unique_recipes']} |\n";
        $content .= "| % Hoàn thành | {$statistics['completion_rate']}% |\n";
        $content .= "| Tổng calories | " . number_format($statistics['total_calories']) . " kcal |\n";
        $content .= "| Tổng chi phí | " . number_format($statistics['total_cost']) . " VNĐ |\n\n";

        // Tính năng sử dụng
        $content .= "## ⚙️ Tính năng Sử dụng\n\n";
        $content .= "- 🌤️ **Tối ưu thời tiết:** " . ($mealPlan->weather_optimized ? 'Có' : 'Không') . "\n";
        $content .= "- 🤖 **Sử dụng AI:** " . ($mealPlan->ai_generated ? 'Có' : 'Không') . "\n";
        $content .= "- 🛒 **Đã tạo danh sách mua sắm:** " . ($mealPlan->shopping_list_created ? 'Có' : 'Không') . "\n\n";

        // Danh sách mua sắm
        $shoppingList = $mealPlan->generateShoppingList();
        if (!empty($shoppingList)) {
            $content .= "## 🛒 Danh sách Mua sắm\n\n";
            $content .= "| Nguyên liệu | Số lượng | Đơn vị | Công thức sử dụng |\n";
            $content .= "|-------------|----------|--------|-------------------|\n";

            foreach ($shoppingList as $item) {
                $content .= "| {$item['name']} | {$item['quantity']} | {$item['unit']} | {$item['recipes']} |\n";
            }
            $content .= "\n";
        }

        // Bữa ăn theo ngày
        $days = [
            'monday' => 'Thứ 2',
            'tuesday' => 'Thứ 3',
            'wednesday' => 'Thứ 4',
            'thursday' => 'Thứ 5',
            'friday' => 'Thứ 6',
            'saturday' => 'Thứ 7',
            'sunday' => 'Chủ nhật'
        ];

        $mealTypes = WeeklyMealPlan::getMealTypes();
        $content .= "## 🍽️ Kế hoạch Bữa ăn Theo Ngày\n\n";

        foreach ($days as $dayKey => $dayLabel) {
            $dayDate = $mealPlan->week_start->copy()->addDays(array_search($dayKey, array_keys($days)));
            $content .= "### {$dayLabel} - {$dayDate->format('d/m/Y')}\n\n";

            $hasMeals = false;
            foreach ($mealTypes as $mealType => $mealTypeLabel) {
                $meals = $mealPlan->getMealsForDay($dayKey, $mealType);

                if (!empty($meals)) {
                    $hasMeals = true;
                    $content .= "#### {$mealTypeLabel}\n\n";

                    foreach ($meals as $recipeId) {
                        $recipe = \App\Models\Recipe::find($recipeId);
                        if ($recipe) {
                            $content .= "**{$recipe->name}**\n\n";
                            $content .= "> {$recipe->description}\n\n";

                            $content .= "**Thông tin:**\n";
                            $content .= "- 🕒 Thời gian nấu: {$recipe->cooking_time} phút\n";
                            $content .= "- ⭐ Độ khó: {$recipe->difficulty}/5\n";
                            $content .= "- 👥 Khẩu phần: {$recipe->servings} người\n";
                            $content .= "- 🔥 Calories: " . number_format($recipe->calories) . " kcal\n\n";

                            // Nguyên liệu
                            if ($recipe->ingredients->count() > 0) {
                                $content .= "**Nguyên liệu:**\n";
                                foreach ($recipe->ingredients as $ingredient) {
                                    $content .= "- {$ingredient->pivot->quantity} {$ingredient->pivot->unit} {$ingredient->name}\n";
                                }
                                $content .= "\n";
                            }

                            // Hướng dẫn nấu
                            if ($recipe->instructions->count() > 0) {
                                $content .= "**Hướng dẫn nấu:**\n";
                                foreach ($recipe->instructions as $instruction) {
                                    $content .= "{$instruction->step}. {$instruction->content}\n";
                                }
                                $content .= "\n";
                            }

                            $content .= "---\n\n";
                        }
                    }
                }
            }

            if (!$hasMeals) {
                $content .= "*Không có bữa ăn nào được lên kế hoạch cho ngày này.*\n\n";
            }
        }

        // Footer
        $content .= "---\n\n";
        $content .= "*Tài liệu được tạo tự động từ hệ thống quản lý kế hoạch bữa ăn*\n";
        $content .= "*Ngày xuất: " . now()->format('d/m/Y H:i:s') . "*\n";

        return $content;
    }

    private function exportAllMealPlans()
    {
        $mealPlans = WeeklyMealPlan::where('user_id', $this->user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $content = '';

        // Header
        $content .= "# Danh sách Kế hoạch Bữa ăn\n\n";
        $content .= "**Người dùng:** {$this->user->name} ({$this->user->email})\n";
        $content .= "**Tổng số kế hoạch:** {$mealPlans->count()}\n";
        $content .= "**Ngày xuất:** " . now()->format('d/m/Y H:i:s') . "\n\n";

        // Thống kê tổng hợp
        $totalMeals = 0;
        $totalCalories = 0;
        $totalCost = 0;
        $activePlans = 0;
        $weatherOptimized = 0;
        $aiGenerated = 0;
        $shoppingListCreated = 0;

        foreach ($mealPlans as $mealPlan) {
            $stats = $mealPlan->getStatistics();
            $totalMeals += $stats['total_meals'];
            $totalCalories += $stats['total_calories'];
            $totalCost += $stats['total_cost'];
            if ($mealPlan->is_active)
                $activePlans++;
            if ($mealPlan->weather_optimized)
                $weatherOptimized++;
            if ($mealPlan->ai_generated)
                $aiGenerated++;
            if ($mealPlan->shopping_list_created)
                $shoppingListCreated++;
        }

        $content .= "## 📊 Thống kê Tổng hợp\n\n";
        $content .= "| Chỉ số | Giá trị |\n";
        $content .= "|--------|---------|\n";
        $content .= "| Tổng số kế hoạch | {$mealPlans->count()} |\n";
        $content .= "| Kế hoạch hoạt động | {$activePlans} |\n";
        $content .= "| Tổng số bữa ăn | {$totalMeals} |\n";
        $content .= "| Tổng calories | " . number_format($totalCalories) . " kcal |\n";
        $content .= "| Tổng chi phí | " . number_format($totalCost) . " VNĐ |\n";
        $content .= "| Trung bình bữa/kế hoạch | " . ($mealPlans->count() > 0 ? round($totalMeals / $mealPlans->count(), 2) : 0) . " |\n\n";

        $content .= "## ⚙️ Tính năng Sử dụng\n\n";
        $content .= "- 🌤️ **Tối ưu thời tiết:** {$weatherOptimized} kế hoạch\n";
        $content .= "- 🤖 **Sử dụng AI:** {$aiGenerated} kế hoạch\n";
        $content .= "- 🛒 **Đã tạo danh sách mua sắm:** {$shoppingListCreated} kế hoạch\n\n";

        // Danh sách kế hoạch
        $content .= "## 📋 Danh sách Kế hoạch\n\n";

        foreach ($mealPlans as $index => $mealPlan) {
            $stats = $mealPlan->getStatistics();
            $content .= "### " . ($index + 1) . ". {$mealPlan->name}\n\n";

            $content .= "**Thông tin cơ bản:**\n";
            $content .= "- 📅 Tuần: {$mealPlan->week_start->format('d/m/Y')} - {$mealPlan->week_end->format('d/m/Y')}\n";
            $content .= "- 🏷️ Trạng thái: " . ($mealPlan->is_active ? 'Hoạt động' : 'Không hoạt động') . "\n";
            $content .= "- 📅 Ngày tạo: {$mealPlan->created_at->format('d/m/Y H:i')}\n\n";

            $content .= "**Thống kê:**\n";
            $content .= "- 🍽️ Số bữa ăn: {$stats['total_meals']}\n";
            $content .= "- 📝 Công thức duy nhất: {$stats['unique_recipes']}\n";
            $content .= "- ✅ % Hoàn thành: {$stats['completion_rate']}%\n";
            $content .= "- 🔥 Calories: " . number_format($stats['total_calories']) . " kcal\n";
            $content .= "- 💰 Chi phí: " . number_format($stats['total_cost']) . " VNĐ\n\n";

            $content .= "**Tính năng:**\n";
            $content .= "- 🌤️ Tối ưu thời tiết: " . ($mealPlan->weather_optimized ? 'Có' : 'Không') . "\n";
            $content .= "- 🤖 Sử dụng AI: " . ($mealPlan->ai_generated ? 'Có' : 'Không') . "\n";
            $content .= "- 🛒 Danh sách mua sắm: " . ($mealPlan->shopping_list_created ? 'Có' : 'Không') . "\n\n";

            if ($index < $mealPlans->count() - 1) {
                $content .= "---\n\n";
            }
        }

        // Footer
        $content .= "---\n\n";
        $content .= "*Tài liệu được tạo tự động từ hệ thống quản lý kế hoạch bữa ăn*\n";
        $content .= "*Ngày xuất: " . now()->format('d/m/Y H:i:s') . "*\n";

        return $content;
    }
}
