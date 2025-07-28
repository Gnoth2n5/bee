<?php

namespace App\Filament\Widgets;

use App\Models\Rating;
use Filament\Widgets\ChartWidget;

class RatingDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Phân bố đánh giá';
    protected static ?int $sort = 8;

    protected function getData(): array
    {
        $ratings = collect();
        $labels = collect();

        for ($i = 1; $i <= 5; $i++) {
            $count = Rating::where('rating', $i)->count();
            $ratings->push($count);
            $labels->push($i . '⭐');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng đánh giá',
                    'data' => $ratings->toArray(),
                    'backgroundColor' => [
                        '#ef4444', // 1 sao - đỏ
                        '#f97316', // 2 sao - cam
                        '#eab308', // 3 sao - vàng
                        '#84cc16', // 4 sao - xanh lá
                        '#10b981', // 5 sao - xanh dương
                    ],
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}