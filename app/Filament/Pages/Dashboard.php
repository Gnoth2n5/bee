<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Rating;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function getTitle(): string
    {
        return 'Bảng điều khiển BeeFood';
    }

    public function getHeading(): string
    {
        return 'Chào mừng đến với BeeFood Admin';
    }

    public function getSubheading(): string
    {
        return 'Quản lý hệ thống công thức nấu ăn';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\UserStatsWidget::class,
            \App\Filament\Widgets\QuickStatsCards::class,
            \App\Filament\Widgets\SystemHealthCards::class,
            \App\Filament\Widgets\PerformanceCards::class,
            \App\Filament\Widgets\SystemInfoCards::class,
            \App\Filament\Widgets\ModerationStatsWidget::class,
            \App\Filament\Widgets\CategoryTagStatsWidget::class,
            \App\Filament\Widgets\ScheduledApprovalsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\LatestRecipes::class,
            \App\Filament\Widgets\TopUsersWidget::class,
            \App\Filament\Widgets\UserGrowthChart::class,
            \App\Filament\Widgets\RecipeStatusChart::class,
            \App\Filament\Widgets\TopCategoriesChart::class,
            \App\Filament\Widgets\RatingDistributionChart::class,
            \App\Filament\Widgets\RecentActivityWidget::class,
            \App\Filament\Widgets\ModerationRulesWidget::class,
            \App\Filament\Widgets\PopularCategoriesWidget::class,
        ];
    }
}