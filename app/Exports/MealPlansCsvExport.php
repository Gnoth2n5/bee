<?php

namespace App\Exports;

use App\Models\WeeklyMealPlan;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class MealPlansCsvExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $user;
    protected $mealPlan;

    public function __construct(User $user = null, WeeklyMealPlan $mealPlan = null)
    {
        $this->user = $user;
        $this->mealPlan = $mealPlan;
    }

    public function collection()
    {
        if ($this->mealPlan) {
            // Export single meal plan
            return collect([$this->mealPlan]);
        }

        // Export all meal plans for user
        return WeeklyMealPlan::where('user_id', $this->user->id)
            ->orderBy('week_start', 'desc')
            ->get();
    }

    public function headings(): array
    {
        if ($this->mealPlan) {
            return [
                'Ngày',
                'Thứ',
                'Loại bữa ăn',
                'Tên món ăn',
                'Mô tả',
                'Calories',
                'Thời gian nấu',
                'Độ khó',
                'Nguyên liệu',
                'Hướng dẫn nấu'
            ];
        }

        return [
            'Tên kế hoạch',
            'Tuần bắt đầu',
            'Tuần kết thúc',
            'Trạng thái',
            'Tổng calories',
            'Tổng chi phí (VNĐ)',
            'Số bữa ăn',
            '% Hoàn thành',
            'Tối ưu thời tiết',
            'Sử dụng AI',
            'Đã tạo danh sách mua sắm',
            'Ngày tạo',
            'Ngày cập nhật'
        ];
    }

    public function map($item): array
    {
        if ($this->mealPlan) {
            // Export detailed meal plan
            return $this->mapMealPlanDetails($item);
        }

        // Export meal plan summary
        return $this->mapMealPlanSummary($item);
    }

    private function mapMealPlanDetails(WeeklyMealPlan $mealPlan): array
    {
        $data = [];
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

        foreach ($days as $dayKey => $dayLabel) {
            $dayDate = $mealPlan->week_start->copy()->addDays(array_search($dayKey, array_keys($days)));

            foreach ($mealTypes as $mealType => $mealTypeLabel) {
                $meals = $mealPlan->getMealsForDay($dayKey, $mealType);

                if (!empty($meals)) {
                    foreach ($meals as $recipeId) {
                        $recipe = \App\Models\Recipe::find($recipeId);
                        if ($recipe) {
                            $data[] = [
                                $dayDate->format('d/m/Y'),
                                $dayLabel,
                                $mealTypeLabel,
                                $recipe->title,
                                $recipe->description,
                                $recipe->calories_per_serving,
                                $recipe->cooking_time . ' phút',
                                $recipe->difficulty,
                                $this->formatIngredients($recipe),
                                $this->formatInstructions($recipe)
                            ];
                        }
                    }
                } else {
                    $data[] = [
                        $dayDate->format('d/m/Y'),
                        $dayLabel,
                        $mealTypeLabel,
                        'Chưa có món ăn',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    ];
                }
            }
        }

        return $data;
    }

    private function mapMealPlanSummary(WeeklyMealPlan $mealPlan): array
    {
        $statistics = $mealPlan->getStatistics();

        return [
            $mealPlan->name,
            $mealPlan->week_start->format('d/m/Y'),
            $mealPlan->week_start->addDays(6)->format('d/m/Y'),
            $mealPlan->is_active ? 'Hoạt động' : 'Không hoạt động',
            number_format($mealPlan->total_calories),
            number_format($mealPlan->total_cost),
            $statistics['total_meals'],
            $statistics['completion_percentage'] . '%',
            $mealPlan->weather_optimized ? 'Có' : 'Không',
            $mealPlan->ai_suggestions_used ? 'Có' : 'Không',
            $mealPlan->shopping_list_generated ? 'Có' : 'Không',
            $mealPlan->created_at->format('d/m/Y H:i'),
            $mealPlan->updated_at->format('d/m/Y H:i')
        ];
    }

    private function formatIngredients($recipe): string
    {
        if (!isset($recipe->ingredients) || !is_array($recipe->ingredients)) {
            return '';
        }

        $ingredients = [];
        foreach ($recipe->ingredients as $ingredient) {
            $ingredients[] = ($ingredient['amount'] ?? '') . ' ' .
                ($ingredient['unit'] ?? '') . ' ' .
                ($ingredient['name'] ?? '');
        }

        return implode('; ', $ingredients);
    }

    private function formatInstructions($recipe): string
    {
        if (!isset($recipe->instructions) || !is_array($recipe->instructions)) {
            return '';
        }

        $instructions = [];
        foreach ($recipe->instructions as $index => $instruction) {
            $instructions[] = ($index + 1) . '. ' . ($instruction['step'] ?? '');
        }

        return implode('; ', $instructions);
    }

    public function title(): string
    {
        if ($this->mealPlan) {
            return 'Chi tiết kế hoạch bữa ăn - ' . $this->mealPlan->name;
        }

        return 'Danh sách kế hoạch bữa ăn - ' . $this->user->name;
    }
}
