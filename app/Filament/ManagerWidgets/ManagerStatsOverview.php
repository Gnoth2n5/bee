<?php

namespace App\Filament\ManagerWidgets;

use App\Models\Recipe;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Widget tổng quan thống kê cho Manager
 * 
 * Hiển thị các số liệu quan trọng mà Manager cần theo dõi:
 * - Công thức chờ duyệt
 * - Bài viết đã xuất bản
 * - Hoạt động trong tuần
 */
class ManagerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            // Công thức của người khác chờ duyệt
            Stat::make('Công thức chờ duyệt', Recipe::where('status', 'pending')
                ->where('user_id', '!=', $userId)
                ->count())
                ->description('Của người khác')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            // Công thức của chính mình
            Stat::make('Công thức của tôi', Recipe::where('user_id', $userId)->count())
                ->description('Tổng số')
                ->descriptionIcon('heroicon-o-cake')
                ->color('info'),

            // Bài viết của chính mình
            Stat::make('Bài viết của tôi', Post::where('user_id', $userId)->count())
                ->description('Tổng số')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success'),

            // Bài viết đã xuất bản của mình
            Stat::make('Đã xuất bản', Post::where('user_id', $userId)
                ->where('status', 'published')
                ->count())
                ->description('Bài viết của tôi')
                ->descriptionIcon('heroicon-o-globe-alt')
                ->color('primary'),
        ];
    }
}
