<?php

namespace App\Filament\UserResources\UserRecipeResource\Pages;

use App\Filament\UserResources\UserRecipeResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

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