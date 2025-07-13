<?php

namespace App\Filament\UserResources\UserRecipeResource\Pages;

use App\Filament\UserResources\UserRecipeResource;
use Filament\Resources\Pages\EditRecord;
use App\Services\RecipeService;

class EditUserRecipe extends EditRecord
{
    protected static string $resource = UserRecipeResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $recipeService = app(RecipeService::class);
        return $recipeService->update($record, $data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['category_ids'] = $this->record->categories->pluck('id')->toArray();
        $data['tag_ids'] = $this->record->tags->pluck('id')->toArray();
        return $data;
    }
    
} 