<?php

namespace App\Filament\UserPages;

use Filament\Pages\Page;
use App\Models\Recipe;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class UserDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.user-pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\UserWidgets\UserWelcomeHeaderWidget::class,
            \App\Filament\UserWidgets\UserPersonalStats::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\UserWidgets\UserRecentRecipesWidget::class,
            \App\Filament\UserWidgets\UserRecipeChart::class,
            \App\Filament\UserWidgets\UserRecipeStatusChart::class,
            \App\Filament\UserWidgets\UserRatingChart::class,
        ];
    }
    protected static ?string $title = 'Bảng điều khiển';
    protected static ?string $navigationLabel = 'Bảng điều khiển';
    protected static ?int $navigationSort = 0;

    public $totalRecipes = 0;
    public $totalPosts = 0;
    public $totalFavorites = 0;
    public $totalRatings = 0;
    public $latestRecipe = null;
    public $latestPost = null;
    public $recentActivities = [];

    public function mount()
    {
        $user = Auth::user();
        $this->totalRecipes = Recipe::where('user_id', $user->id)->count();
        $this->totalPosts = Post::where('user_id', $user->id)->count();
        $this->totalFavorites = \App\Models\Favorite::where('user_id', $user->id)->count();
        $this->totalRatings = \App\Models\Rating::where('user_id', $user->id)->count();
        $this->latestRecipe = Recipe::where('user_id', $user->id)->latest()->first();
        $this->latestPost = Post::where('user_id', $user->id)->latest()->first();
        $this->loadRecentActivities();
    }

    public function loadRecentActivities()
    {
        $user = Auth::user();
        $activities = collect();

        // Công thức mới
        $recentRecipes = Recipe::where('user_id', $user->id)->latest()->limit(3)->get();
        foreach ($recentRecipes as $recipe) {
            $activities->push([
                'type' => 'recipe',
                'title' => 'Công thức: ' . $recipe->title,
                'description' => 'Trạng thái: ' . $recipe->status,
                'time' => $recipe->created_at,
                'icon' => 'heroicon-o-cake',
                'color' => 'text-green-600',
            ]);
        }

        // Bài viết mới
        $recentPosts = Post::where('user_id', $user->id)->latest()->limit(3)->get();
        foreach ($recentPosts as $post) {
            $activities->push([
                'type' => 'post',
                'title' => 'Bài viết: ' . $post->title,
                'description' => 'Trạng thái: ' . $post->status,
                'time' => $post->created_at,
                'icon' => 'heroicon-o-document-text',
                'color' => 'text-blue-600',
            ]);
        }

        // Đánh giá mới
        $recentRatings = \App\Models\Rating::where('user_id', $user->id)->with('recipe')->latest()->limit(3)->get();
        foreach ($recentRatings as $rating) {
            $activities->push([
                'type' => 'rating',
                'title' => 'Đánh giá: ' . $rating->rating . '⭐',
                'description' => 'Công thức: ' . $rating->recipe->title,
                'time' => $rating->created_at,
                'icon' => 'heroicon-o-star',
                'color' => 'text-yellow-600',
            ]);
        }

        $this->recentActivities = $activities->sortByDesc('time')->take(8)->values();
    }
}