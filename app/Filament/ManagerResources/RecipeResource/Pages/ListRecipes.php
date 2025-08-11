<?php

namespace App\Filament\ManagerResources\RecipeResource\Pages;

use App\Filament\ManagerResources\RecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecipes extends ListRecords
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo công thức mới'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Loại bỏ biểu đồ, chỉ giữ danh sách
        ];
    }
}
