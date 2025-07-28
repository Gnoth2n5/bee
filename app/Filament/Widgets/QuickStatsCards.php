<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Rating;
use App\Models\Post;
use App\Models\Collection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class QuickStatsCards extends BaseWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $thisMonth = $now->format('Y-m');

        return [
            Stat::make('Công thức hôm nay', Recipe::whereDate('created_at', $today)->count())
                ->description('Tạo trong ngày hôm nay')
                ->descriptionIcon('heroicon-m-cake')
                ->color('success'),

            Stat::make('Người dùng mới hôm nay', User::whereDate('created_at', $today)->count())
                ->description('Đăng ký trong ngày hôm nay')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary'),

            Stat::make('Đánh giá hôm nay', Rating::whereDate('created_at', $today)->count())
                ->description('Đánh giá trong ngày hôm nay')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Bài viết hôm nay', Post::whereDate('created_at', $today)->count())
                ->description('Đăng trong ngày hôm nay')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Bộ sưu tập mới', Collection::whereDate('created_at', $today)->count())
                ->description('Tạo trong ngày hôm nay')
                ->descriptionIcon('heroicon-m-folder-plus')
                ->color('secondary'),

            Stat::make('Công thức chờ duyệt', Recipe::where('status', 'pending')->count())
                ->description('Cần phê duyệt ngay')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}