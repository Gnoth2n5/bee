<?php

namespace App\Filament\Resources\ModerationRuleResource\Pages;

use App\Filament\Resources\ModerationRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditModerationRule extends EditRecord
{
    protected static string $resource = ModerationRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa quy tắc')
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}