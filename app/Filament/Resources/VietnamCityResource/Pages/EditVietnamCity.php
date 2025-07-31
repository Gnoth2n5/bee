<?php

namespace App\Filament\Resources\VietnamCityResource\Pages;

use App\Filament\Resources\VietnamCityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVietnamCity extends EditRecord
{
    protected static string $resource = VietnamCityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}