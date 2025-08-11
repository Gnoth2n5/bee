<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\LatestRecipes;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
// use App\Http\Middleware\FilamentPanelAccess; // Tạm thời không dùng

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('BeeFood Admin')
            ->brandLogo(asset('images/bee-logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/bee-favicon.ico'))
            ->sidebarCollapsibleOnDesktop()
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('Về web')
                    ->url('/')
                    ->icon('heroicon-o-home')
                    ->sort(100)
            ])
            ->colors([
                'primary' => [
                    50 => '#fff7ed',
                    100 => '#ffedd5',
                    200 => '#fed7aa',
                    300 => '#fdba74',
                    400 => '#fb923c',
                    500 => '#ff6b35', // BeeFood brand color
                    600 => '#ea580c',
                    700 => '#c2410c',
                    800 => '#9a3412',
                    900 => '#7c2d12',
                    950 => '#431407',
                ],
                'gray' => [
                    50 => '#f9fafb',
                    100 => '#f3f4f6',
                    200 => '#e5e7eb',
                    300 => '#d1d5db',
                    400 => '#9ca3af',
                    500 => '#6b7280',
                    600 => '#4b5563',
                    700 => '#374151',
                    800 => '#1f2937',
                    900 => '#111827',
                    950 => '#030712',
                ],
            ])
            ->font('Inter')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverview::class,
                LatestRecipes::class,
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                // FilamentPanelAccess::class . ':admin', // Tạm thời không dùng
            ])
            ->authGuard('web');
    }
}
