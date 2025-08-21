<?php

namespace App\Filament\ManagerResources\RecipeResource\Pages;

use App\Filament\ManagerResources\RecipeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRecipe extends CreateRecord
{
    protected static string $resource = RecipeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
}
