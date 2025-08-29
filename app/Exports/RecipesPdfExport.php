<?php

namespace App\Exports;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class RecipesPdfExport
{
    protected $user;
    protected $filters;

    public function __construct(User $user = null, array $filters = [])
    {
        $this->user = $user;
        $this->filters = $filters;
    }

    public function export()
    {
        $recipes = $this->getRecipes();
        $html = $this->generateHtml($recipes);

        // Sử dụng DomPDF để tạo PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function getRecipes()
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

    private function generateHtml($recipes): string
    {
        $html = '<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách công thức</title>
    <style>
        body { 
            font-family: DejaVu Sans, Arial, sans-serif; 
            font-size: 12px; 
            line-height: 1.4; 
            color: #333; 
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #FF6B35; 
            padding-bottom: 20px; 
            margin-bottom: 30px; 
        }
        .header h1 { 
            color: #FF6B35; 
            margin: 0; 
            font-size: 24px; 
        }
        .header p { 
            margin: 10px 0 0 0; 
            color: #666; 
        }
        .recipe { 
            margin-bottom: 30px; 
            page-break-inside: avoid; 
            border: 1px solid #ddd; 
            padding: 15px; 
            border-radius: 8px; 
        }
        .recipe h2 { 
            color: #FF6B35; 
            margin: 0 0 10px 0; 
            font-size: 18px; 
            border-bottom: 1px solid #FF6B35; 
            padding-bottom: 5px; 
        }
        .recipe-info { 
            display: table; 
            width: 100%; 
            margin: 10px 0; 
        }
        .recipe-info-row { 
            display: table-row; 
        }
        .recipe-info-cell { 
            display: table-cell; 
            padding: 5px 10px; 
            border: 1px solid #ddd; 
            background: #f8f9fa; 
        }
        .recipe-info-label { 
            font-weight: bold; 
            width: 30%; 
        }
        .ingredients { 
            margin: 15px 0; 
        }
        .ingredients h3 { 
            color: #FF6B35; 
            margin: 0 0 10px 0; 
            font-size: 14px; 
        }
        .ingredient-item { 
            margin: 5px 0; 
            padding: 5px; 
            background: #f8f9fa; 
            border-radius: 3px; 
        }
        .instructions { 
            margin: 15px 0; 
        }
        .instructions h3 { 
            color: #FF6B35; 
            margin: 0 0 10px 0; 
            font-size: 14px; 
        }
        .instruction-step { 
            margin: 8px 0; 
            padding: 8px; 
            background: #fff3cd; 
            border-left: 3px solid #FF6B35; 
        }
        .tips { 
            margin: 15px 0; 
            padding: 10px; 
            background: #d4edda; 
            border-radius: 5px; 
            border-left: 3px solid #28a745; 
        }
        .tips h3 { 
            color: #155724; 
            margin: 0 0 5px 0; 
            font-size: 14px; 
        }
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #ddd; 
            color: #666; 
            font-size: 10px; 
        }
        .page-break { 
            page-break-before: always; 
        }
    </style>
</head>
<body>';

        $html .= '<div class="header">
            <h1>DANH SÁCH CÔNG THỨC</h1>
            <p>Ngày xuất: ' . now()->format('d/m/Y H:i:s') . '</p>';

        if ($this->user) {
            $html .= '<p>Người dùng: ' . htmlspecialchars($this->user->name) . '</p>';
        }

        $html .= '<p>Tổng số công thức: ' . $recipes->count() . '</p>
        </div>';

        foreach ($recipes as $index => $recipe) {
            if ($index > 0 && $index % 3 == 0) {
                $html .= '<div class="page-break"></div>';
            }

            $html .= $this->generateRecipeHtml($recipe);
        }

        $html .= '<div class="footer">
            <p>Được tạo bởi hệ thống quản lý công thức nấu ăn</p>
            <p>Trang ' . (isset($dompdf) ? $dompdf->getCanvas()->get_page_number() : '1') . '</p>
        </div>';

        $html .= '</body></html>';

        return $html;
    }

    private function generateRecipeHtml($recipe): string
    {
        $html = '<div class="recipe">
            <h2>' . htmlspecialchars($recipe->title) . '</h2>';

        if ($recipe->description) {
            $html .= '<p><em>' . htmlspecialchars($recipe->description) . '</em></p>';
        }

        $html .= '<div class="recipe-info">
            <div class="recipe-info-row">
                <div class="recipe-info-cell recipe-info-label">Thời gian nấu:</div>
                <div class="recipe-info-cell">' . ($recipe->cooking_time ?? 0) . ' phút</div>
                <div class="recipe-info-cell recipe-info-label">Độ khó:</div>
                <div class="recipe-info-cell">' . $this->getDifficultyLabel($recipe->difficulty) . '</div>
            </div>
            <div class="recipe-info-row">
                <div class="recipe-info-cell recipe-info-label">Khẩu phần:</div>
                <div class="recipe-info-cell">' . $recipe->servings . ' người</div>
                <div class="recipe-info-cell recipe-info-label">Calories:</div>
                <div class="recipe-info-cell">' . ($recipe->calories_per_serving ?? 'N/A') . '</div>
            </div>
            <div class="recipe-info-row">
                <div class="recipe-info-cell recipe-info-label">Trạng thái:</div>
                <div class="recipe-info-cell">' . $this->getStatusLabel($recipe->status) . '</div>
                <div class="recipe-info-cell recipe-info-label">Tác giả:</div>
                <div class="recipe-info-cell">' . htmlspecialchars($recipe->user ? $recipe->user->name : 'N/A') . '</div>
            </div>
        </div>';

        // Nguyên liệu
        $html .= '<div class="ingredients">
            <h3>Nguyên liệu</h3>';

        if ($recipe->ingredients) {
            $ingredients = is_string($recipe->ingredients) ? json_decode($recipe->ingredients, true) : $recipe->ingredients;
            if (is_array($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    if (is_array($ingredient)) {
                        $name = $ingredient['name'] ?? '';
                        $amount = $ingredient['amount'] ?? '';
                        $unit = $ingredient['unit'] ?? '';
                        if ($name) {
                            $html .= '<div class="ingredient-item">• ' . htmlspecialchars($name) . ': ' . htmlspecialchars($amount) . ' ' . htmlspecialchars($unit) . '</div>';
                        }
                    }
                }
            }
        } else {
            $html .= '<p>Không có thông tin nguyên liệu.</p>';
        }

        $html .= '</div>';

        // Hướng dẫn nấu
        $html .= '<div class="instructions">
            <h3>Hướng dẫn nấu</h3>';

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
        } else {
            $html .= '<p>Không có hướng dẫn nấu.</p>';
        }

        $html .= '</div>';

        // Mẹo nấu
        if ($recipe->tips) {
            $html .= '<div class="tips">
                <h3>Mẹo nấu</h3>
                <p>' . htmlspecialchars($recipe->tips) . '</p>
            </div>';
        }

        $html .= '</div>';

        return $html;
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

