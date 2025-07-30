<?php

namespace App\Filament\Resources\VietnamCityResource\Pages;

use App\Filament\Resources\VietnamCityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVietnamCities extends ListRecords
{
    protected static string $resource = VietnamCityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('seed_cities')
                ->label('Tạo dữ liệu mẫu')
                ->icon('heroicon-o-plus-circle')
                ->color('info')
                ->action(function () {
                    $seeder = new \Database\Seeders\VietnamCitySeeder();
                    $seeder->run();

                    \Filament\Notifications\Notification::make()
                        ->title('Đã tạo dữ liệu 52 tỉnh thành Việt Nam')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('update_all_weather')
                ->label('Cập nhật thời tiết tất cả')
                ->icon('heroicon-o-cloud')
                ->color('success')
                ->action(function () {
                    $weatherService = new \App\Services\WeatherService();
                    $cities = \App\Models\VietnamCity::active()->get();
                    $count = 0;

                    foreach ($cities as $city) {
                        $weatherService->getCurrentWeather($city);
                        $count++;
                    }

                    \Filament\Notifications\Notification::make()
                        ->title("Đã cập nhật thời tiết cho $count thành phố")
                        ->success()
                        ->send();
                }),
        ];
    }
}