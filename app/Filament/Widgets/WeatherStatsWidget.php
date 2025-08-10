<?php

namespace App\Filament\Widgets;

use App\Models\WeatherData;
use App\Models\VietnamCity;
use App\Models\WeatherConditionRule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WeatherStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCities = VietnamCity::active()->count();
        $citiesWithWeather = WeatherData::where('last_updated', '>=', now()->subHours(6))->distinct('city_code')->count();
        $totalRules = WeatherConditionRule::count();
        $activeRules = WeatherConditionRule::active()->count();

        return [
            Stat::make('Tổng số thành phố', $totalCities)
                ->description('Tỉnh thành đang hoạt động')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Thành phố có dữ liệu thời tiết', $citiesWithWeather)
                ->description('Cập nhật trong 6 giờ qua')
                ->descriptionIcon('heroicon-m-cloud')
                ->color($citiesWithWeather >= $totalCities * 0.8 ? 'success' : 'warning'),

            Stat::make('Quy tắc đề xuất', $totalRules)
                ->description('Tổng số quy tắc')
                ->descriptionIcon('heroicon-m-light-bulb')
                ->color('info'),

            Stat::make('Quy tắc đang hoạt động', $activeRules)
                ->description('Quy tắc đang được sử dụng')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($activeRules > 0 ? 'success' : 'gray'),
        ];
    }
}