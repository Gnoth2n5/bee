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
        
        // Đảm bảo keywords được format đúng
        if (isset($data['keywords'])) {
            $keywords = array_map('trim', explode(',', $data['keywords']));
            $keywords = array_filter($keywords);
            $data['keywords'] = implode(', ', $keywords);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}