<?php

namespace App\Filament\Resources\ModerationRuleResource\Pages;

use App\Filament\Resources\ModerationRuleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateModerationRule extends CreateRecord
{
    protected static string $resource = ModerationRuleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}