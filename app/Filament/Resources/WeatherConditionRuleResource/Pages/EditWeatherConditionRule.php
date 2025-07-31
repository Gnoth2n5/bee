<?php

namespace App\Filament\Resources\WeatherConditionRuleResource\Pages;

use App\Filament\Resources\WeatherConditionRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeatherConditionRule extends EditRecord
{
    protected static string $resource = WeatherConditionRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 