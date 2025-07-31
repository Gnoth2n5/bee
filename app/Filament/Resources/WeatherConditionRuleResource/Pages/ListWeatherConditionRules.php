<?php

namespace App\Filament\Resources\WeatherConditionRuleResource\Pages;

use App\Filament\Resources\WeatherConditionRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeatherConditionRules extends ListRecords
{
    protected static string $resource = WeatherConditionRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo Quy Tắc Mới'),
        ];
    }
} 