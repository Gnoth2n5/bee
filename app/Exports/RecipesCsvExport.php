<?php

namespace App\Exports;

use App\Models\Recipe;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class RecipesCsvExport implements FromCollection, WithHeadings, WithMapping, WithTitle
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
        $query = Recipe::with(['user', 'categories', 'tags']);

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

        return implode("; ", $formatted);
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

        return implode("; ", $formatted);
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
