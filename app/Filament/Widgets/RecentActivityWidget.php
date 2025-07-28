<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Rating;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class RecentActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-activity-widget';
    protected static ?int $sort = 6;

    public function getRecentActivities()
    {
        $activities = collect();

        // Người dùng mới
        $newUsers = User::latest()->limit(3)->get();
        foreach ($newUsers as $user) {
            $activities->push([
                'type' => 'user',
                'title' => 'Người dùng mới: ' . $user->name,
                'description' => 'Đăng ký lúc ' . $user->created_at->format('d/m/Y H:i'),
                'icon' => 'heroicon-o-user-plus',
                'color' => 'text-blue-600',
                'time' => $user->created_at,
            ]);
        }

        // Công thức mới
        $newRecipes = Recipe::latest()->limit(3)->get();
        foreach ($newRecipes as $recipe) {
            $activities->push([
                'type' => 'recipe',
                'title' => 'Công thức mới: ' . $recipe->title,
                'description' => 'Bởi ' . $recipe->user->name . ' - ' . $recipe->status,
                'icon' => 'heroicon-o-cake',
                'color' => 'text-green-600',
                'time' => $recipe->created_at,
            ]);
        }

        // Đánh giá mới
        $newRatings = Rating::with('user', 'recipe')->latest()->limit(3)->get();
        foreach ($newRatings as $rating) {
            $activities->push([
                'type' => 'rating',
                'title' => 'Đánh giá mới: ' . $rating->rating . '⭐',
                'description' => $rating->user->name . ' đánh giá ' . $rating->recipe->title,
                'icon' => 'heroicon-o-star',
                'color' => 'text-yellow-600',
                'time' => $rating->created_at,
            ]);
        }

        return $activities->sortByDesc('time')->take(8);
    }
}