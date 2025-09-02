<?php

namespace App\Filament\Resources\VipPostApprovalResource\Pages;

use App\Filament\Resources\VipPostApprovalResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;

class ViewVipPostApproval extends ViewRecord
{
    protected static string $resource = VipPostApprovalResource::class;

    public function getTitle(): string
    {
        return 'Chi tiết bài viết VIP';
    }

    protected function getHeaderActions(): array
    {
        return [
            // ACTION: Duyệt bài viết
            Actions\Action::make('approve')
                ->label('Duyệt & Xuất bản')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Duyệt bài viết VIP')
                ->modalDescription('Bạn có chắc chắn muốn duyệt và xuất bản bài viết này?')
                ->modalSubmitActionLabel('Duyệt & Xuất bản')
                ->action(function () {
                    $this->record->update([
                        'status' => 'published',
                        'published_at' => now(),
                        'rejection_reason' => null, // Xóa lý do từ chối cũ
                    ]);

                    $this->refreshFormData(['status', 'published_at', 'rejection_reason']);
                })
                ->visible(fn() => in_array($this->record->status, ['pending', 'draft'])),

            // ACTION: Từ chối bài viết
            Actions\Action::make('reject')
                ->label('Từ chối')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')
                        ->label('Lý do từ chối')
                        ->required()
                        ->rows(3)
                        ->helperText('Lý do sẽ được hiển thị cho tác giả VIP'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Từ chối bài viết VIP')
                ->modalSubmitActionLabel('Từ chối')
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'draft',
                        'rejection_reason' => $data['rejection_reason'],
                    ]);

                    $this->refreshFormData(['status', 'rejection_reason']);

                    // TODO: Gửi notification cho tác giả VIP
                })
                ->visible(fn() => $this->record->status === 'pending'),

            // ACTION: Lưu trữ bài viết
            Actions\Action::make('archive')
                ->label('Lưu trữ')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Lưu trữ bài viết VIP')
                ->modalDescription('Bài viết sẽ được ẩn khỏi trang web')
                ->modalSubmitActionLabel('Lưu trữ')
                ->action(function () {
                    $this->record->update([
                        'status' => 'archived',
                    ]);

                    $this->refreshFormData(['status']);
                })
                ->visible(fn() => $this->record->status === 'published'),

            // ACTION: Khôi phục từ lưu trữ
            Actions\Action::make('unarchive')
                ->label('Khôi phục')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Khôi phục bài viết VIP')
                ->modalDescription('Bài viết sẽ được xuất bản lại')
                ->modalSubmitActionLabel('Khôi phục')
                ->action(function () {
                    $this->record->update([
                        'status' => 'published',
                        'published_at' => $this->record->published_at ?? now(),
                    ]);

                    $this->refreshFormData(['status', 'published_at']);
                })
                ->visible(fn() => $this->record->status === 'archived'),
        ];
    }
}
