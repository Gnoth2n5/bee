<?php

namespace App\Filament\Widgets;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Rating;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class PerformanceCards extends BaseWidget
{
    protected static ?int $sort = 12;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $lastWeek = $now->copy()->subWeek();
        $lastMonth = $now->copy()->subMonth();

        // Tính tăng trưởng công thức
        $recipesThisWeek = Recipe::whereBetween('created_at', [$lastWeek, $now])->count();
        $recipesLastWeek = Recipe::whereBetween('created_at', [$lastWeek->copy()->subWeek(), $lastWeek])->count();
        $recipeGrowth = $recipesLastWeek > 0 ? round((($recipesThisWeek - $recipesLastWeek) / $recipesLastWeek) * 100, 1) : 0;

        // Tính tăng trưởng user
        $usersThisWeek = User::whereBetween('created_at', [$lastWeek, $now])->count();
        $usersLastWeek = User::whereBetween('created_at', [$lastWeek->copy()->subWeek(), $lastWeek])->count();
        $userGrowth = $usersLastWeek > 0 ? round((($usersThisWeek - $usersLastWeek) / $usersLastWeek) * 100, 1) : 0;

        // Tính tăng trưởng đánh giá
        $ratingsThisWeek = Rating::whereBetween('created_at', [$lastWeek, $now])->count();
        $ratingsLastWeek = Rating::whereBetween('created_at', [$lastWeek->copy()->subWeek(), $lastWeek])->count();
        $ratingGrowth = $ratingsLastWeek > 0 ? round((($ratingsThisWeek - $ratingsLastWeek) / $ratingsLastWeek) * 100, 1) : 0;

        // Tính công thức phổ biến nhất
        $popularRecipe = Recipe::orderBy('view_count', 'desc')->first();

        return [
            Stat::make('Tăng trưởng công thức', $recipeGrowth . '%')
                ->description('So với tuần trước')
                ->descriptionIcon('heroicon-m-cake')
                ->color($recipeGrowth >= 0 ? 'success' : 'danger')
                ->chart([
                    Recipe::whereBetween('created_at', [$lastMonth->startOfWeek(), $lastMonth->endOfWeek()])->count(),
                    Recipe::whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])->count(),
                ]),

            Stat::make('Tăng trưởng user', $userGrowth . '%')
                ->description('So với tuần trước')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($userGrowth >= 0 ? 'success' : 'danger')
                ->chart([
                    User::whereBetween('created_at', [$lastMonth->startOfWeek(), $lastMonth->endOfWeek()])->count(),
                    User::whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])->count(),
                ]),

            Stat::make('Tăng trưởng đánh giá', $ratingGrowth . '%')
                ->description('So với tuần trước')
                ->descriptionIcon('heroicon-m-star')
                ->color($ratingGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Công thức phổ biến', $popularRecipe ? $popularRecipe->view_count . ' lượt xem' : '0 lượt xem')
                ->description($popularRecipe ? $popularRecipe->title : 'Chưa có')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
        ];
    }
}