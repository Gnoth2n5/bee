<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Rating;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class UserStatsWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();
        $lastWeek = $now->copy()->subWeek();

        return [
            Stat::make('Tổng người dùng', User::count())
                ->description('Tất cả người dùng đã đăng ký')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([
                    User::whereBetween('created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])->count(),
                    User::whereBetween('created_at', [$now->startOfMonth(), $now->endOfMonth()])->count(),
                ]),

            Stat::make('Người dùng mới (tháng này)', User::whereMonth('created_at', $now->month)->count())
                ->description('Đăng ký trong tháng ' . $now->format('m/Y'))
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success')
                ->chart([
                    User::whereBetween('created_at', [$lastWeek->startOfWeek(), $lastWeek->endOfWeek()])->count(),
                    User::whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()])->count(),
                ]),

            Stat::make('Người dùng hoạt động', User::where('status', 'active')->count())
                ->description('Trạng thái hoạt động')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Người dùng bị cấm', User::where('status', 'banned')->count())
                ->description('Tài khoản bị cấm')
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color('danger'),

            Stat::make('Đã xác thực email', User::whereNotNull('email_verified_at')->count())
                ->description('Email đã xác thực')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('info'),

            Stat::make('Người dùng có công thức', User::has('recipes')->count())
                ->description('Đã đăng công thức')
                ->descriptionIcon('heroicon-m-cake')
                ->color('warning'),
        ];
    }
}