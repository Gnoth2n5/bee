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
            // 8 card thống kê
            \App\Filament\Widgets\DashboardStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // 2 biểu đồ
            \App\Filament\Widgets\RecipeStatusChart::class,
            \App\Filament\Widgets\UserGrowthChart::class,

            // 1 bảng công thức mới
            \App\Filament\Widgets\RecentRecipesWidget::class,

            // 1 bảng hoạt động gần đây
            \App\Filament\Widgets\RecentActivityWidget::class,
        ];
    }
}
