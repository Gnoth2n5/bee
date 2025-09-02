<?php

namespace App\Filament\ManagerResources\VipPostApprovalResource\Pages;

use App\Filament\ManagerResources\VipPostApprovalResource;
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
                ->modalHeading('Duyệt bài viết')
                ->modalDescription('Bạn có chắc chắn muốn duyệt và xuất bản bài viết này?')
                ->modalSubmitActionLabel('Duyệt & Xuất bản')
                ->action(function () {
                    $this->record->update([
                        'status' => 'published',
                        'published_at' => now(),
                    ]);

                    $this->refreshFormData(['status', 'published_at']);
                })
                ->visible(fn() => $this->record->status === 'pending'),

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
                        ->helperText('Lý do sẽ được gửi cho tác giả'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Từ chối bài viết')
                ->modalSubmitActionLabel('Từ chối')
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'draft',
                        'rejection_reason' => $data['rejection_reason'],
                    ]);

                    $this->refreshFormData(['status']);

                    // TODO: Gửi notification cho tác giả
                })
                ->visible(fn() => $this->record->status === 'pending'),

            // ACTION: Lưu trữ bài viết
            Actions\Action::make('archive')
                ->label('Lưu trữ')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Lưu trữ bài viết')
                ->modalDescription('Bài viết sẽ được ẩn khỏi trang web')
                ->modalSubmitActionLabel('Lưu trữ')
                ->action(function () {
                    $this->record->update([
                        'status' => 'archived',
                    ]);

                    $this->refreshFormData(['status']);
                })
                ->visible(fn() => $this->record->status === 'published'),
        ];
    }
}
