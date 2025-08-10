<?php

namespace App\Filament\UserWidgets;

use App\Models\Recipe;
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
        $totalFavorites = Favorite::where('user_id', $user->id)->count();
        $totalRatings = Rating::where('user_id', $user->id)->count();
        $totalCollections = Collection::where('user_id', $user->id)->count();

        // Thống kê trạng thái
        $approvedRecipes = Recipe::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();



        return [
            Stat::make('Tổng công thức', $totalRecipes)
                ->description('Công thức đã tạo')
                ->descriptionIcon('heroicon-m-cake')
                ->color('primary'),

            Stat::make('Công thức đã duyệt', $approvedRecipes)
                ->description('Được phê duyệt')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

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