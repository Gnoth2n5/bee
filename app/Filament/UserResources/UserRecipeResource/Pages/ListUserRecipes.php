<?php

namespace App\Filament\UserResources\UserRecipeResource\Pages;

use App\Filament\UserResources\UserRecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserRecipes extends ListRecords
{
    protected static string $resource = UserRecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}