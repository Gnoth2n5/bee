<?php

namespace App\Filament\UserResources\UserRecipeResource\Pages;

use App\Filament\UserResources\UserRecipeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Services\RecipeService;

class CreateUserRecipe extends CreateRecord
{
    protected static string $resource = UserRecipeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Đảm bảo user_id là user hiện tại
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $recipeService = app(RecipeService::class);
        $user = Auth::user();
        return $recipeService->create($data, $user);
    }
} 