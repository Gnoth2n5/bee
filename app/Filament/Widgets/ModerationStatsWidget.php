<?php

namespace App\Filament\Widgets;

use App\Models\ModerationRule;
use App\Models\Recipe;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ModerationStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalRules = ModerationRule::count();
        $activeRules = ModerationRule::where('is_active', true)->count();
        $pendingRecipes = Recipe::where('status', 'pending')->count();
        $rejectedRecipes = Recipe::where('status', 'rejected')->count();
        $flaggedRecipes = Recipe::where('status', 'pending')
            ->whereNotNull('rejection_reason')
            ->where('rejection_reason', 'like', 'FLAGGED:%')
            ->count();

        return [
            Stat::make('Quy tắc kiểm duyệt', $totalRules)
                ->description($activeRules . ' đang hoạt động')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),

            Stat::make('Công thức chờ duyệt', $pendingRecipes)
                ->description('Cần kiểm tra')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Công thức bị từ chối', $rejectedRecipes)
                ->description('Tự động từ chối')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Công thức đánh dấu', $flaggedRecipes)
                ->description('Cần kiểm tra thủ công')
                ->descriptionIcon('heroicon-m-flag')
                ->color('info'),
        ];
    }
}