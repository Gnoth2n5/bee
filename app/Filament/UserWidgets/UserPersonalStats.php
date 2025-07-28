<?php

namespace App\Filament\UserWidgets;

use App\Models\Recipe;
use App\Models\Post;
use App\Models\Rating;
use App\Models\Favorite;
use App\Models\Collection;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class UserPersonalStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $now = Carbon::now();
        $thisMonth = $now->format('Y-m');
        $lastMonth = $now->copy()->subMonth()->format('Y-m');

        // Thống kê tổng quan
        $totalRecipes = Recipe::where('user_id', $user->id)->count();
        $totalPosts = Post::where('user_id', $user->id)->count();
        $totalFavorites = Favorite::where('user_id', $user->id)->count();
        $totalRatings = Rating::where('user_id', $user->id)->count();
        $totalCollections = Collection::where('user_id', $user->id)->count();

        // Thống kê tháng này
        $recipesThisMonth = Recipe::where('user_id', $user->id)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        $postsThisMonth = Post::where('user_id', $user->id)
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        // Thống kê trạng thái
        $approvedRecipes = Recipe::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $pendingRecipes = Recipe::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $publishedPosts = Post::where('user_id', $user->id)
            ->where('status', 'published')
            ->count();

        return [
            Stat::make('Tổng công thức', $totalRecipes)
                ->description('Công thức đã tạo')
                ->descriptionIcon('heroicon-m-cake')
                ->color('primary')
                ->chart([
                    Recipe::where('user_id', $user->id)
                        ->whereYear('created_at', $now->year)
                        ->whereMonth('created_at', $now->copy()->subMonth()->month)
                        ->count(),
                    $recipesThisMonth,
                ]),

            Stat::make('Công thức tháng này', $recipesThisMonth)
                ->description('Tạo trong tháng ' . $now->format('m/Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Công thức đã duyệt', $approvedRecipes)
                ->description('Được phê duyệt')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Công thức chờ duyệt', $pendingRecipes)
                ->description('Đang chờ phê duyệt')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Tổng bài viết', $totalPosts)
                ->description('Bài viết đã đăng')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart([
                    Post::where('user_id', $user->id)
                        ->whereYear('created_at', $now->year)
                        ->whereMonth('created_at', $now->copy()->subMonth()->month)
                        ->count(),
                    $postsThisMonth,
                ]),

            Stat::make('Bài viết đã xuất bản', $publishedPosts)
                ->description('Đã xuất bản')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info'),

            Stat::make('Công thức yêu thích', $totalFavorites)
                ->description('Đã lưu yêu thích')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),

            Stat::make('Đánh giá đã viết', $totalRatings)
                ->description('Đánh giá đã gửi')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Bộ sưu tập', $totalCollections)
                ->description('Bộ sưu tập đã tạo')
                ->descriptionIcon('heroicon-m-folder')
                ->color('secondary'),
        ];
    }
}