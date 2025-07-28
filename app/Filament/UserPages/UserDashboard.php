<?php

namespace App\Filament\UserPages;

use Filament\Pages\Page;
use App\Models\Recipe;
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
            \App\Filament\UserWidgets\UserRecipeStatusChart::class,
            \App\Filament\UserWidgets\UserRatingChart::class,
        ];
    }
    protected static ?string $title = 'Bảng điều khiển';
    protected static ?string $navigationLabel = 'Bảng điều khiển';
    protected static ?int $navigationSort = 0;




}