<?php

namespace App\Filament\UserResources\UserPostResource\Pages;

use App\Filament\UserResources\UserPostResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Services\PostService;

class CreateUserPost extends CreateRecord
{
    protected static string $resource = UserPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Đảm bảo user_id là user hiện tại
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $postService = app(PostService::class);
        return $postService->createPost($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}