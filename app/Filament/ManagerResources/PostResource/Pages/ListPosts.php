<?php

namespace App\Filament\ManagerResources\PostResource\Pages;

use App\Filament\ManagerResources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo bài viết mới'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Loại bỏ biểu đồ, chỉ giữ danh sách
        ];
    }
}
