<?php

namespace App\Exports;

use App\Models\Recipe;
use App\Models\User;
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

class RecipesExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    protected $user;
    protected $filters;

    public function __construct(User $user = null, array $filters = [])
    {
        $this->user = $user;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Recipe::with(['user', 'categories', 'tags', 'images', 'ratings']);

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

    public function headings(): array
    {
        return [
            'ID',
            'Tên công thức',
            'Mô tả',
            'Tóm tắt',
            'Thời gian nấu (phút)',
            'Thời gian chuẩn bị (phút)',
            'Tổng thời gian (phút)',
            'Độ khó',
            'Khẩu phần',
            'Calories/khẩu phần',
            'Nguyên liệu',
            'Hướng dẫn nấu',
            'Mẹo nấu',
            'Ghi chú',
            'Danh mục',
            'Tags',
            'Trạng thái',
            'Lượt xem',
            'Lượt yêu thích',
            'Đánh giá trung bình',
            'Số lượt đánh giá',
            'Tác giả',
            'Ngày tạo',
            'Ngày cập nhật',
            'Ngày xuất bản'
        ];
    }

    public function map($recipe): array
    {
        return [
            $recipe->id,
            $recipe->title,
            $recipe->description,
            $recipe->summary,
            $recipe->cooking_time,
            $recipe->preparation_time,
            $recipe->total_time,
            $this->getDifficultyLabel($recipe->difficulty),
            $recipe->servings,
            $recipe->calories_per_serving,
            $this->formatIngredients($recipe),
            $this->formatInstructions($recipe),
            $recipe->tips,
            $recipe->notes,
            $this->formatCategories($recipe),
            $this->formatTags($recipe),
            $this->getStatusLabel($recipe->status),
            $recipe->view_count,
            $recipe->favorite_count,
            $recipe->average_rating,
            $recipe->rating_count,
            $recipe->user ? $recipe->user->name : 'N/A',
            $recipe->created_at ? $recipe->created_at->format('d/m/Y H:i') : '',
            $recipe->updated_at ? $recipe->updated_at->format('d/m/Y H:i') : '',
            $recipe->published_at ? $recipe->published_at->format('d/m/Y H:i') : ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style cho header
        $sheet->getStyle('A1:Y1')->applyFromArray([
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
            $sheet->getStyle('A2:Y' . $lastRow)->applyFromArray([
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

            // Style cho các dòng theo trạng thái
            for ($row = 2; $row <= $lastRow; $row++) {
                $status = $sheet->getCell('Q' . $row)->getValue();
                if ($status === 'Đã duyệt') {
                    $sheet->getStyle('A' . $row . ':Y' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D4EDDA'],
                        ],
                    ]);
                } elseif ($status === 'Chờ duyệt') {
                    $sheet->getStyle('A' . $row . ':Y' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFF3CD'],
                        ],
                    ]);
                } elseif ($status === 'Bị từ chối') {
                    $sheet->getStyle('A' . $row . ':Y' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8D7DA'],
                        ],
                    ]);
                }
            }
        }

        // Wrap text cho các cột có nội dung dài
        $sheet->getStyle('C:L')->getAlignment()->setWrapText(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // ID
            'B' => 30, // Tên công thức
            'C' => 40, // Mô tả
            'D' => 30, // Tóm tắt
            'E' => 15, // Thời gian nấu
            'F' => 20, // Thời gian chuẩn bị
            'G' => 18, // Tổng thời gian
            'H' => 12, // Độ khó
            'I' => 12, // Khẩu phần
            'J' => 18, // Calories
            'K' => 50, // Nguyên liệu
            'L' => 60, // Hướng dẫn nấu
            'M' => 40, // Mẹo nấu
            'N' => 40, // Ghi chú
            'O' => 25, // Danh mục
            'P' => 25, // Tags
            'Q' => 12, // Trạng thái
            'R' => 12, // Lượt xem
            'S' => 15, // Lượt yêu thích
            'T' => 15, // Đánh giá trung bình
            'U' => 15, // Số lượt đánh giá
            'V' => 20, // Tác giả
            'W' => 18, // Ngày tạo
            'X' => 18, // Ngày cập nhật
            'Y' => 18, // Ngày xuất bản
        ];
    }

    public function title(): string
    {
        $title = 'Danh sách công thức';
        if ($this->user) {
            $title .= ' - ' . $this->user->name;
        }
        return $title;
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

    private function formatCategories($recipe): string
    {
        return $recipe->categories->pluck('name')->implode(', ');
    }

    private function formatTags($recipe): string
    {
        return $recipe->tags->pluck('name')->implode(', ');
    }
}

