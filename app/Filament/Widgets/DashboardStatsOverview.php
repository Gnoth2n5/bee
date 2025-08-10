<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Rating;
use App\Models\ModerationRule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalRecipes = Recipe::count();
        $pendingRecipes = Recipe::where('status', 'pending')->count();
        $approvedRecipes = Recipe::where('status', 'approved')->count();
        $totalRatings = Rating::count();
        $activeRules = ModerationRule::where('is_active', true)->count();

        return [
            Stat::make('Tổng người dùng', $totalUsers)
                ->description('Người dùng đã đăng ký')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Công thức chờ duyệt', $pendingRecipes)
                ->description('Cần kiểm tra')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Công thức đã duyệt', $approvedRecipes)
                ->description('Đã phê duyệt')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Tổng công thức', $totalRecipes)
                ->description('Tất cả công thức')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Đánh giá', $totalRatings)
                ->description('Tổng đánh giá')
                ->descriptionIcon('heroicon-m-star')
                ->color('yellow'),

            Stat::make('Quy tắc kiểm duyệt', $activeRules)
                ->description('Đang hoạt động')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),
        ];
    }
} 