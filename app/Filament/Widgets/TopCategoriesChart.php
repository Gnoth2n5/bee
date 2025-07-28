<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class TopCategoriesChart extends ChartWidget
{
    protected static ?string $heading = 'Top danh mục công thức';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $categories = Category::withCount('recipes')
            ->orderBy('recipes_count', 'desc')
            ->limit(8)
            ->get();

        $labels = $categories->pluck('name')->toArray();
        $data = $categories->pluck('recipes_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Số công thức',
                    'data' => $data,
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#06b6d4',
                        '#84cc16',
                        '#f97316',
                    ],
                    'borderWidth' => 1,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}