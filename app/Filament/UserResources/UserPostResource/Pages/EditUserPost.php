<?php

namespace App\Filament\UserResources\UserPostResource\Pages;

use App\Filament\UserResources\UserPostResource;
use Filament\Resources\Pages\EditRecord;
use App\Services\PostService;

class EditUserPost extends EditRecord
{
    protected static string $resource = UserPostResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $postService = app(PostService::class);
        return $postService->updatePost($record, $data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}