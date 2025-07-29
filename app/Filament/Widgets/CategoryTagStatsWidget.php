<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Tag;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoryTagStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        $totalTags = Tag::count();
        $popularTags = Tag::where('usage_count', '>', 0)->count();

        return [
            Stat::make('Tổng danh mục', $totalCategories)
                ->description($activeCategories . ' đang hoạt động')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Tổng thẻ', $totalTags)
                ->description($popularTags . ' đang được sử dụng')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),

            Stat::make('Danh mục chính', Category::whereNull('parent_id')->count())
                ->description('Danh mục gốc')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Danh mục con', Category::whereNotNull('parent_id')->count())
                ->description('Danh mục phụ')
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('warning'),
        ];
    }
}