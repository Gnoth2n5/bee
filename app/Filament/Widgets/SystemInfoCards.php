<?php

namespace App\Filament\Widgets;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Rating;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SystemInfoCards extends BaseWidget
{
    protected static ?int $sort = 13;

    protected function getStats(): array
    {
        // Tính tổng dung lượng database
        $totalRecipes = Recipe::count();
        $totalUsers = User::count();
        $totalRatings = Rating::count();
        $totalPosts = Post::count();
        $totalCategories = Category::count();
        $totalTags = Tag::count();

        // Tính công thức trung bình mỗi user
        $avgRecipesPerUser = $totalUsers > 0 ? round($totalRecipes / $totalUsers, 1) : 0;

        // Tính đánh giá trung bình mỗi công thức
        $avgRatingsPerRecipe = $totalRecipes > 0 ? round($totalRatings / $totalRecipes, 1) : 0;

        // Tính tỷ lệ công thức có ảnh
        $recipesWithImages = Recipe::whereNotNull('featured_image')->count();
        $imageRate = $totalRecipes > 0 ? round(($recipesWithImages / $totalRecipes) * 100, 1) : 0;

        return [
            Stat::make('Tổng dữ liệu', number_format($totalRecipes + $totalUsers + $totalRatings + $totalPosts))
                ->description('Tổng số bản ghi trong hệ thống')
                ->descriptionIcon('heroicon-m-server')
                ->color('primary'),

            Stat::make('Công thức/User', $avgRecipesPerUser)
                ->description('Trung bình công thức mỗi user')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),

            Stat::make('Đánh giá/Công thức', $avgRatingsPerRecipe)
                ->description('Trung bình đánh giá mỗi công thức')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Công thức có ảnh', $imageRate . '%')
                ->description('Tỷ lệ công thức có ảnh')
                ->descriptionIcon('heroicon-m-camera')
                ->color('info'),

            Stat::make('Danh mục & Tags', $totalCategories . ' + ' . $totalTags)
                ->description('Danh mục và thẻ phân loại')
                ->descriptionIcon('heroicon-m-tag')
                ->color('secondary'),

            Stat::make('Tổng bài viết', number_format($totalPosts))
                ->description('Bài viết trong hệ thống')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
        ];
    }
}