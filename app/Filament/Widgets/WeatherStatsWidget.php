<?php

namespace App\Filament\Widgets;

use App\Models\WeatherData;
use App\Models\VietnamCity;
use App\Models\WeatherRecipeSuggestion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WeatherStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCities = VietnamCity::active()->count();
        $citiesWithWeather = WeatherData::where('last_updated', '>=', now()->subHours(6))->distinct('city_code')->count();
        $totalSuggestions = WeatherRecipeSuggestion::active()->count();
        $recentSuggestions = WeatherRecipeSuggestion::where('last_generated', '>=', now()->subDay())->count();

        return [
            Stat::make('Tổng số thành phố', $totalCities)
                ->description('Tỉnh thành đang hoạt động')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Thành phố có dữ liệu thời tiết', $citiesWithWeather)
                ->description('Cập nhật trong 6 giờ qua')
                ->descriptionIcon('heroicon-m-cloud')
                ->color($citiesWithWeather >= $totalCities * 0.8 ? 'success' : 'warning'),

            Stat::make('Đề xuất món ăn', $totalSuggestions)
                ->description('Đang hoạt động')
                ->descriptionIcon('heroicon-m-light-bulb')
                ->color('info'),

            Stat::make('Đề xuất mới', $recentSuggestions)
                ->description('Tạo trong 24 giờ qua')
                ->descriptionIcon('heroicon-m-clock')
                ->color($recentSuggestions > 0 ? 'success' : 'gray'),
        ];
    }
}