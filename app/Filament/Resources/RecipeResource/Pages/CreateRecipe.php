<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use App\Services\RecipeService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRecipe extends CreateRecord
{
    protected static string $resource = RecipeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Xử lý ingredients và instructions để đảm bảo đúng format
        if (isset($data['ingredients']) && is_array($data['ingredients'])) {
            $data['ingredients'] = array_values(array_filter($data['ingredients'], function ($item) {
                return !empty($item['name']) && !empty($item['amount']);
            }));
        }

        if (isset($data['instructions']) && is_array($data['instructions'])) {
            $data['instructions'] = array_values(array_filter($data['instructions'], function ($item) {
                return !empty($item['instruction']);
            }));
            
            // Đánh số lại các bước
            foreach ($data['instructions'] as $index => &$instruction) {
                $instruction['step'] = $index + 1;
            }
        }

        // Xử lý category_ids và tag_ids
        if (isset($data['category_ids'])) {
            $data['category_ids'] = is_array($data['category_ids']) ? $data['category_ids'] : [$data['category_ids']];
        }

        if (isset($data['tag_ids'])) {
            $data['tag_ids'] = is_array($data['tag_ids']) ? $data['tag_ids'] : [$data['tag_ids']];
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $recipeService = app(RecipeService::class);
        $user = Auth::user();
        
        return $recipeService->create($data, $user);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
