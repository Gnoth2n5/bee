<?php

namespace App\Filament\Widgets;

use App\Models\Recipe;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ScheduledApprovalsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $now = now();

        // Công thức đang chờ phê duyệt theo lịch trình
        $pendingScheduled = Recipe::where('status', 'pending')
            ->whereNotNull('auto_approve_at')
            ->count();

        // Công thức sẽ được phê duyệt trong 1 giờ tới
        $nextHour = Recipe::where('status', 'pending')
            ->whereNotNull('auto_approve_at')
            ->where('auto_approve_at', '<=', $now->addHour())
            ->where('auto_approve_at', '>', $now)
            ->count();

        // Công thức sẽ được phê duyệt trong 24 giờ tới
        $next24Hours = Recipe::where('status', 'pending')
            ->whereNotNull('auto_approve_at')
            ->where('auto_approve_at', '<=', $now->addDay())
            ->where('auto_approve_at', '>', $now)
            ->count();

        return [
            Stat::make('Chờ phê duyệt theo lịch', $pendingScheduled)
                ->description('Công thức có lịch phê duyệt')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Phê duyệt trong 1h', $nextHour)
                ->description('Sẽ được phê duyệt trong 1 giờ tới')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Phê duyệt trong 24h', $next24Hours)
                ->description('Sẽ được phê duyệt trong 24 giờ tới')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
        ];
    }
}