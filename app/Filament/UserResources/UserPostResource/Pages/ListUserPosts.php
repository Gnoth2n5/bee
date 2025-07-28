<?php

namespace App\Filament\UserResources\UserPostResource\Pages;

use App\Filament\UserResources\UserPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserPosts extends ListRecords
{
    protected static string $resource = UserPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo bài viết mới'),
        ];
    }
}