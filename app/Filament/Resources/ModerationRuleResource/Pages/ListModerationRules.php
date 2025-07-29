<?php

namespace App\Filament\Resources\ModerationRuleResource\Pages;

use App\Filament\Resources\ModerationRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModerationRules extends ListRecords
{
    protected static string $resource = ModerationRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo quy tắc mới')
                ->icon('heroicon-o-plus'),
        ];
    }
}