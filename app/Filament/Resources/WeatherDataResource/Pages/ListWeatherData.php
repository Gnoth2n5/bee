<?php

namespace App\Filament\Resources\WeatherDataResource\Pages;

use App\Filament\Resources\WeatherDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeatherData extends ListRecords
{
    protected static string $resource = WeatherDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('update_all_weather')
                ->label('Cập nhật tất cả thời tiết')
                ->icon('heroicon-o-arrow-path')
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