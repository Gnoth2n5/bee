<?php

namespace App\Filament\UserWidgets;

use App\Models\Rating;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class UserRatingChart extends ChartWidget
{
    protected static ?string $heading = 'Phân bố đánh giá của tôi';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 1;
    protected static ?int $contentHeight = 1;

    protected function getData(): array
    {
        $user = Auth::user();
        $ratings = collect();
        $labels = collect();

        for ($i = 1; $i <= 5; $i++) {
            $count = Rating::where('user_id', $user->id)->where('rating', $i)->count();
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

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 1,
                        'maxTicksLimit' => 5,
                    ],
                ],
            ],
        ];
    }
}