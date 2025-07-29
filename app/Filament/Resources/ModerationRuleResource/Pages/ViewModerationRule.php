<?php

namespace App\Filament\Resources\ModerationRuleResource\Pages;

use App\Filament\Resources\ModerationRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewModerationRule extends ViewRecord
{
    protected static string $resource = ModerationRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Chỉnh sửa')
                ->icon('heroicon-o-pencil'),
        ];
    }
}