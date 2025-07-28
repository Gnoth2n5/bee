<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UserGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Tăng trưởng người dùng';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $days = collect();
        $users = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('d/m'));
            $users->push(User::whereDate('created_at', $date)->count());
        }

        return [
            'datasets' => [
                [
                    'label' => 'Người dùng mới',
                    'data' => $users->toArray(),
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
}