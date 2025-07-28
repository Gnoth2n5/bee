<?php

namespace App\Filament\UserWidgets;

use App\Models\Recipe;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class UserRecipeChart extends ChartWidget
{
    protected static ?string $heading = 'Tăng trưởng công thức của tôi';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;
    protected static ?int $contentHeight = 2;

    protected function getData(): array
    {
        $user = Auth::user();
        $days = collect();
        $recipes = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('d/m'));
            $recipes->push(Recipe::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->count());
        }

        return [
            'datasets' => [
                [
                    'label' => 'Công thức mới',
                    'data' => $recipes->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
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