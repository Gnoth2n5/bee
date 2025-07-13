<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use App\Services\RecipeService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EditRecipe extends EditRecord
{
    protected static string $resource = RecipeResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Lấy trực tiếp từ quan hệ đã load
        $data['category_ids'] = $this->record->categories->pluck('id')->toArray();
        $data['tag_ids'] = $this->record->tags->pluck('id')->toArray();

        Log::info('DEBUG category_ids:', [$data['category_ids']]);
        Log::info('DEBUG tag_ids:', [$data['tag_ids']]);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $recipeService = app(RecipeService::class);
        
        return $recipeService->update($record, $data);
    }

    public function getRecord(): \Illuminate\Database\Eloquent\Model
    {
        return parent::getRecord()->load(['categories', 'tags']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    $recipeService = app(RecipeService::class);
                    $recipeService->delete($this->record);
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}