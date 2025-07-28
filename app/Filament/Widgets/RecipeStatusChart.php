<?php

namespace App\Filament\Widgets;

use App\Models\Recipe;
use Filament\Widgets\ChartWidget;

class RecipeStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Trạng thái công thức';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $statuses = [
            'draft' => Recipe::where('status', 'draft')->count(),
            'pending' => Recipe::where('status', 'pending')->count(),
            'approved' => Recipe::where('status', 'approved')->count(),
            'rejected' => Recipe::where('status', 'rejected')->count(),
        ];

        $labels = [
            'draft' => 'Bản nháp',
            'pending' => 'Chờ phê duyệt',
            'approved' => 'Đã phê duyệt',
            'rejected' => 'Từ chối',
        ];

        $colors = [
            'draft' => '#6b7280',
            'pending' => '#f59e0b',
            'approved' => '#10b981',
            'rejected' => '#ef4444',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng',
                    'data' => array_values($statuses),
                    'backgroundColor' => array_values($colors),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => array_values($labels),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}