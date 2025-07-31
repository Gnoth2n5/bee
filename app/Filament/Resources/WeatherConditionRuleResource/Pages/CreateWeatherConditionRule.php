<?php

namespace App\Filament\Resources\WeatherConditionRuleResource\Pages;

use App\Filament\Resources\WeatherConditionRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWeatherConditionRule extends CreateRecord
{
    protected static string $resource = WeatherConditionRuleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 