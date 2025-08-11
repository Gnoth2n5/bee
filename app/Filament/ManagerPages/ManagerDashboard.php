<?php

namespace App\Filament\ManagerPages;

use Filament\Pages\Dashboard;

/**
 * Dashboard chính dành cho Manager
 * 
 * Dashboard này hiển thị tổng quan về các hoạt động quan trọng
 * mà Manager cần theo dõi:
 * - Số lượng công thức chờ duyệt
 * - Thống kê bài viết
 * - Hoạt động gần đây
 */
class ManagerDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.manager.pages.dashboard';

    protected static ?string $title = 'Tổng quan Manager';

    protected static ?string $navigationLabel = 'Tổng quan';

    protected static ?int $navigationSort = 1;

    public function getWidgets(): array
    {
        return [
            \App\Filament\ManagerWidgets\ManagerStatsOverview::class,
            \App\Filament\ManagerWidgets\PendingRecipes::class,
            \App\Filament\ManagerWidgets\MyRecentPosts::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
