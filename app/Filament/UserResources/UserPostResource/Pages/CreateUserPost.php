<?php

namespace App\Filament\UserResources\UserPostResource\Pages;

use App\Filament\UserResources\UserPostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUserPost extends CreateRecord
{
    protected static string $resource = UserPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Tự động gán user_id cho bài viết mới
        $data['user_id'] = Auth::id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Chuyển hướng về trang danh sách sau khi tạo thành công
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Bài viết đã được tạo thành công!';
    }

    public function getTitle(): string
    {
        return 'Tạo bài viết mới';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Tạo bài viết'),
            $this->getCreateAnotherFormAction()
                ->label('Tạo & Tạo tiếp'),
            $this->getCancelFormAction()
                ->label('Hủy'),
        ];
    }
}
