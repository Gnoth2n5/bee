<?php

namespace App\Filament\UserResources\UserPostResource\Pages;

use App\Filament\UserResources\UserPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserPost extends EditRecord
{
    protected static string $resource = UserPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Xem chi tiết'),
            Actions\DeleteAction::make()
                ->label('Xóa bài viết'),

            // Action gửi duyệt
            Actions\Action::make('submit_for_approval')
                ->label('Gửi duyệt')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Gửi bài viết để duyệt')
                ->modalDescription('Bạn có chắc chắn muốn gửi bài viết này để admin phê duyệt?')
                ->modalSubmitActionLabel('Gửi duyệt')
                ->action(function () {
                    $this->record->update([
                        'status' => 'pending',
                    ]);
                    $this->refreshFormData(['status']);
                })
                ->visible(fn() => $this->record->status === 'draft'),

            // Action rút lại yêu cầu duyệt
            Actions\Action::make('withdraw_approval')
                ->label('Rút lại yêu cầu')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Rút lại yêu cầu duyệt')
                ->modalDescription('Bạn có chắc chắn muốn rút lại yêu cầu duyệt bài viết này?')
                ->modalSubmitActionLabel('Rút lại')
                ->action(function () {
                    $this->record->update([
                        'status' => 'draft',
                    ]);
                    $this->refreshFormData(['status']);
                })
                ->visible(fn() => $this->record->status === 'pending'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Chuyển hướng về trang danh sách sau khi cập nhật thành công
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Bài viết đã được cập nhật thành công!';
    }

    public function getTitle(): string
    {
        return 'Chỉnh sửa bài viết';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Lưu thay đổi'),
            $this->getCancelFormAction()
                ->label('Hủy'),
        ];
    }
}
