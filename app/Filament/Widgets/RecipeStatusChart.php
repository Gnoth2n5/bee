<?php

namespace App\Filament\Widgets;

use App\Models\Recipe;
use Filament\Widgets\ChartWidget;

class RecipeStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Trạng thái công thức';

    protected static ?int $sort = 2;

    // Thêm thuộc tính này để biểu đồ chiếm 1/2 chiều rộng
    protected int|string|array $columnSpan = '1/2';

    protected function getData(): array
    {
        $draft = Recipe::where('status', 'draft')->count();
        $pending = Recipe::where('status', 'pending')->count();
        $approved = Recipe::where('status', 'approved')->count();
        $rejected = Recipe::where('status', 'rejected')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Công thức',
                    'data' => [$draft, $pending, $approved, $rejected],
                    'backgroundColor' => [
                        'rgb(156, 163, 175)', // gray - draft
                        'rgb(245, 158, 11)',  // yellow - pending
                        'rgb(34, 197, 94)',   // green - approved
                        'rgb(239, 68, 68)',   // red - rejected
                    ],
                ],
            ],
            'labels' => ['Bản nháp', 'Chờ duyệt', 'Đã duyệt', 'Từ chối'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}