<?php

namespace App\Filament\Resources\WeatherDataResource\Pages;

use App\Filament\Resources\WeatherDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeatherData extends EditRecord
{
    protected static string $resource = WeatherDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}