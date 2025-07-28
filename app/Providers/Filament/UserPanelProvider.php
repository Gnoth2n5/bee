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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\UserMenuItem;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->login()
            ->brandName('BeeFood - Quản lý công thức')
            ->brandLogo(asset('images/bee-logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/bee-favicon.ico'))
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
            ->discoverResources(in: app_path('Filament/UserResources'), for: 'App\\Filament\\UserResources')
            ->discoverPages(in: app_path('Filament/UserPages'), for: 'App\\Filament\\UserPages')
            ->pages([
                \App\Filament\UserPages\UserDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/UserWidgets'), for: 'App\\Filament\\UserWidgets')
            ->widgets([
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
            ])
            ->authGuard('web')
            ->profile()
            ->tenant(null)
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make()
                    ->label('Về trang chủ')
                    ->url(url('/'))
                    ->icon('heroicon-o-home')
                    ->sort(0),
            ]);
    }
}