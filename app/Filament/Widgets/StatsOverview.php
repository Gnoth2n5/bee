<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Rating;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Tổng số người dùng', User::count())
                ->description('Người dùng đã đăng ký')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            
            Stat::make('Tổng số công thức', Recipe::count())
                ->description('Công thức nấu ăn')
                ->descriptionIcon('heroicon-m-cake')
                ->color('success'),
            
            Stat::make('Công thức chờ phê duyệt', Recipe::where('status', 'pending')->count())
                ->description('Cần phê duyệt')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('Tổng số danh mục', Category::count())
                ->description('Danh mục công thức')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('info'),
            
            Stat::make('Tổng số bộ sưu tập', Collection::count())
                ->description('Bộ sưu tập công thức')
                ->descriptionIcon('heroicon-m-folder')
                ->color('secondary'),
            
            Stat::make('Đánh giá trung bình', number_format(Rating::avg('rating') ?? 0, 1))
                ->description('Điểm đánh giá TB')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
} 