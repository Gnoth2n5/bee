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

        // Công thức đang chờ từ chối theo lịch trình
        $pendingRejectScheduled = Recipe::where('status', 'pending')
            ->whereNotNull('auto_reject_at')
            ->count();

        // Công thức sẽ bị từ chối trong 1 giờ tới
        $nextHourReject = Recipe::where('status', 'pending')
            ->whereNotNull('auto_reject_at')
            ->where('auto_reject_at', '<=', $now->addHour())
            ->where('auto_reject_at', '>', $now)
            ->count();

        // Công thức sẽ bị từ chối trong 24 giờ tới
        $next24HoursReject = Recipe::where('status', 'pending')
            ->whereNotNull('auto_reject_at')
            ->where('auto_reject_at', '<=', $now->addDay())
            ->where('auto_reject_at', '>', $now)
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

            Stat::make('Chờ từ chối theo lịch', $pendingRejectScheduled)
                ->description('Công thức có lịch từ chối')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),

            Stat::make('Từ chối trong 1h', $nextHourReject)
                ->description('Sẽ bị từ chối trong 1 giờ tới')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Từ chối trong 24h', $next24HoursReject)
                ->description('Sẽ bị từ chối trong 24 giờ tới')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('danger'),
        ];
    }
}