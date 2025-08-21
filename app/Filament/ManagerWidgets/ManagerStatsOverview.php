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
                ->description('Cần phê duyệt ngay')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            // Công thức của chính mình
            Stat::make('Công thức của tôi', Recipe::where('user_id', $userId)->count())
                ->description('Đã tạo tổng cộng')
                ->descriptionIcon('heroicon-m-cake')
                ->color('info')
                ->chart([3, 8, 5, 10, 15, 8, 12]),

            // Bài viết của chính mình
            Stat::make('Bài viết của tôi', Post::where('user_id', $userId)->count())
                ->description('Đã viết tổng cộng')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->chart([2, 5, 8, 12, 7, 15, 10]),

            // Bài viết đã xuất bản của mình
            Stat::make('Đã xuất bản', Post::where('user_id', $userId)
                ->where('status', 'published')
                ->count())
                ->description('Bài viết công khai')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('primary')
                ->chart([1, 3, 5, 8, 12, 15, 18]),
        ];
    }
}
