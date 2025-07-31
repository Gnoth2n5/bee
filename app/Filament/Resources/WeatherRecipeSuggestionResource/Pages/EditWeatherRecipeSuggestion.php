<?php

namespace App\Filament\Resources\WeatherRecipeSuggestionResource\Pages;

use App\Filament\Resources\WeatherRecipeSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeatherRecipeSuggestion extends EditRecord
{
    protected static string $resource = WeatherRecipeSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}