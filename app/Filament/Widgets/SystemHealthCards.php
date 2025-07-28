<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Rating;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SystemHealthCards extends BaseWidget
{
    protected static ?int $sort = 11;

    protected function getStats(): array
    {
        // Tính tỷ lệ công thức được phê duyệt
        $totalRecipes = Recipe::count();
        $approvedRecipes = Recipe::where('status', 'approved')->count();
        $approvalRate = $totalRecipes > 0 ? round(($approvedRecipes / $totalRecipes) * 100, 1) : 0;

        // Tính tỷ lệ user có hoạt động
        $totalUsers = User::count();
        $activeUsers = User::has('recipes')->orHas('ratings')->orHas('favorites')->count();
        $activityRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;

        // Tính điểm đánh giá trung bình
        $avgRating = Rating::avg('rating') ?? 0;

        // Tính tỷ lệ bài viết được xuất bản
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', 'published')->count();
        $publishRate = $totalPosts > 0 ? round(($publishedPosts / $totalPosts) * 100, 1) : 0;

        return [
            Stat::make('Tỷ lệ phê duyệt', $approvalRate . '%')
                ->description('Công thức được phê duyệt')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($approvalRate >= 80 ? 'success' : ($approvalRate >= 60 ? 'warning' : 'danger')),

            Stat::make('Tỷ lệ hoạt động', $activityRate . '%')
                ->description('User có hoạt động')
                ->descriptionIcon('heroicon-m-users')
                ->color($activityRate >= 70 ? 'success' : ($activityRate >= 50 ? 'warning' : 'danger')),

            Stat::make('Điểm đánh giá TB', number_format($avgRating, 1) . '⭐')
                ->description('Đánh giá trung bình')
                ->descriptionIcon('heroicon-m-star')
                ->color($avgRating >= 4.0 ? 'success' : ($avgRating >= 3.0 ? 'warning' : 'danger')),

            Stat::make('Tỷ lệ xuất bản', $publishRate . '%')
                ->description('Bài viết được xuất bản')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($publishRate >= 80 ? 'success' : ($publishRate >= 60 ? 'warning' : 'danger')),
        ];
    }
}