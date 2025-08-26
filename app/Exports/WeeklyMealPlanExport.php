<?php

namespace App\Exports;

use App\Models\WeeklyMealPlan;
use App\Models\Recipe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class WeeklyMealPlanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $mealPlan;

    public function __construct(WeeklyMealPlan $mealPlan)
    {
        $this->mealPlan = $mealPlan;
    }

    public function collection()
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
            $dayDate = $this->mealPlan->week_start->copy()->addDays(array_search($dayKey, array_keys($days)));
            
            foreach ($mealTypes as $mealType => $mealTypeLabel) {
                $meals = $this->mealPlan->getMealsForDay($dayKey, $mealType);
                
                if (!empty($meals)) {
                    foreach ($meals as $recipeId) {
                        $recipe = Recipe::find($recipeId);
                        if ($recipe) {
                            $data[] = [
                                'day' => $dayLabel,
                                'date' => $dayDate->format('d/m/Y'),
                                'meal_type' => $mealTypeLabel,
                                'recipe_title' => $recipe->title,
                                'description' => $recipe->description,
                                'calories' => $recipe->calories_per_serving,
                                'cooking_time' => $recipe->cooking_time,
                                'difficulty' => $recipe->difficulty,
                                'ingredients' => $this->formatIngredients($recipe),
                                'instructions' => $this->formatInstructions($recipe)
                            ];
                        }
                    }
                } else {
                    // Thêm dòng trống cho bữa ăn chưa có
                    $data[] = [
                        'day' => $dayLabel,
                        'date' => $dayDate->format('d/m/Y'),
                        'meal_type' => $mealTypeLabel,
                        'recipe_title' => 'Chưa có món ăn',
                        'description' => '',
                        'calories' => '',
                        'cooking_time' => '',
                        'difficulty' => '',
                        'ingredients' => '',
                        'instructions' => ''
                    ];
                }
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Ngày trong tuần',
            'Ngày tháng',
            'Loại bữa ăn',
            'Tên món ăn',
            'Mô tả',
            'Calories',
            'Thời gian nấu (phút)',
            'Độ khó',
            'Nguyên liệu',
            'Hướng dẫn nấu'
        ];
    }

    public function map($row): array
    {
        return [
            $row['day'],
            $row['date'],
            $row['meal_type'],
            $row['recipe_title'],
            $row['description'],
            $row['calories'],
            $row['cooking_time'],
            $row['difficulty'],
            $row['ingredients'],
            $row['instructions']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style cho header
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FF6B35'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style cho dữ liệu
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A2:J' . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ]);

            // Style cho các dòng trống (chưa có món ăn)
            for ($row = 2; $row <= $lastRow; $row++) {
                $recipeTitle = $sheet->getCell('D' . $row)->getValue();
                if ($recipeTitle === 'Chưa có món ăn') {
                    $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8F9FA'],
                        ],
                        'font' => [
                            'color' => ['rgb' => '6C757D'],
                        ],
                    ]);
                }
            }
        }

        // Wrap text cho các cột có nội dung dài
        $sheet->getStyle('E:J')->getAlignment()->setWrapText(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Ngày trong tuần
            'B' => 12, // Ngày tháng
            'C' => 15, // Loại bữa ăn
            'D' => 30, // Tên món ăn
            'E' => 40, // Mô tả
            'F' => 10, // Calories
            'G' => 15, // Thời gian nấu
            'H' => 10, // Độ khó
            'I' => 50, // Nguyên liệu
            'J' => 60, // Hướng dẫn nấu
        ];
    }

    public function title(): string
    {
        return 'Kế hoạch bữa ăn tuần ' . $this->mealPlan->week_start->format('d/m/Y');
    }

    private function formatIngredients($recipe): string
    {
        if (!$recipe->ingredients) {
            return '';
        }

        $ingredients = is_string($recipe->ingredients) ? json_decode($recipe->ingredients, true) : $recipe->ingredients;
        
        if (!is_array($ingredients)) {
            return '';
        }

        $formatted = [];
        foreach ($ingredients as $ingredient) {
            if (is_array($ingredient)) {
                $name = $ingredient['name'] ?? '';
                $amount = $ingredient['amount'] ?? '';
                $unit = $ingredient['unit'] ?? '';
                
                if ($name) {
                    $formatted[] = "- {$name}: {$amount} {$unit}";
                }
            }
        }

        return implode("\n", $formatted);
    }

    private function formatInstructions($recipe): string
    {
        if (!$recipe->instructions) {
            return '';
        }

        $instructions = is_string($recipe->instructions) ? json_decode($recipe->instructions, true) : $recipe->instructions;
        
        if (!is_array($instructions)) {
            return $recipe->instructions;
        }

        $formatted = [];
        foreach ($instructions as $index => $instruction) {
            if (is_array($instruction)) {
                $step = $instruction['step'] ?? '';
                if ($step) {
                    $formatted[] = ($index + 1) . ". {$step}";
                }
            } else {
                $formatted[] = ($index + 1) . ". {$instruction}";
            }
        }

        return implode("\n", $formatted);
    }
}
