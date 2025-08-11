<?php

namespace App\Filament\UserWidgets;

use App\Models\Recipe;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class UserRecipeStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Trạng thái công thức';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;
    protected static ?int $contentHeight = 1;

    protected function getData(): array
    {
        $user = Auth::user();

        $statuses = [
            'draft' => Recipe::where('user_id', $user->id)->where('status', 'draft')->count(),
            'pending' => Recipe::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Recipe::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => Recipe::where('user_id', $user->id)->where('status', 'rejected')->count(),
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