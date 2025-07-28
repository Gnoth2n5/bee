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
    protected static ?string $title = 'Bảng điều khiển';
    protected static ?string $navigationLabel = 'Bảng điều khiển';
    protected static ?int $navigationSort = 0;

    public $totalRecipes = 0;
    public $totalPosts = 0;
    public $latestRecipe = null;
    public $latestPost = null;

    public function mount()
    {
        $user = Auth::user();
        $this->totalRecipes = Recipe::where('user_id', $user->id)->count();
        $this->totalPosts = Post::where('user_id', $user->id)->count();
        $this->latestRecipe = Recipe::where('user_id', $user->id)->latest()->first();
        $this->latestPost = Post::where('user_id', $user->id)->latest()->first();
    }
}