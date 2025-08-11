<?php

namespace App\Filament\ManagerPages;

use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;

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

    // Sử dụng Filament dashboard mặc định thay vì custom view
    // protected static string $view = 'filament.manager.pages.dashboard';

    protected static ?string $title = 'Tổng quan Manager';

    protected static ?string $navigationLabel = 'Tổng quan';

    protected static ?int $navigationSort = 1;

    public static function canView(): bool
    {
        // Chỉ cho phép user có role Manager
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return Auth::check() && $user->hasRole('manager');
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\ManagerWidgets\PendingRecipes::class,
            \App\Filament\ManagerWidgets\MyRecentPosts::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\ManagerWidgets\ManagerStatsOverview::class,
        ];
    }
}
