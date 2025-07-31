<?php

namespace App\Filament\Resources\WeatherRecipeSuggestionResource\Pages;

use App\Filament\Resources\WeatherRecipeSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeatherRecipeSuggestions extends ListRecords
{
    protected static string $resource = WeatherRecipeSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('generate_all_suggestions')
                ->label('Tạo đề xuất cho tất cả thành phố')
                ->icon('heroicon-o-light-bulb')
                ->color('success')
                ->action(function () {
                    $weatherRecipeService = new \App\Services\WeatherRecipeService(new \App\Services\WeatherService());
                    $count = $weatherRecipeService->generateAllCitiesSuggestions();

                    \Filament\Notifications\Notification::make()
                        ->title("Đã tạo đề xuất cho $count thành phố")
                        ->success()
                        ->send();
                }),
        ];
    }
}