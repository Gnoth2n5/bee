<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\RestoreAction::make()
                ->label('Khôi phục')
                ->color('success'),
            Actions\DeleteAction::make()
                ->label('Xóa bài viết')
                ->requiresConfirmation()
                ->modalHeading('Xóa bài viết')
                ->modalDescription('Bạn có chắc chắn muốn xóa bài viết này? Bài viết sẽ được chuyển vào thùng rác.')
                ->modalSubmitActionLabel('Xóa')
                ->modalCancelActionLabel('Hủy'),
            Actions\ForceDeleteAction::make()
                ->label('Xóa vĩnh viễn')
                ->requiresConfirmation()
                ->modalHeading('Xóa vĩnh viễn')
                ->modalDescription('Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này? Hành động này không thể hoàn tác.')
                ->modalSubmitActionLabel('Xóa vĩnh viễn')
                ->modalCancelActionLabel('Hủy')
                ->color('danger'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getURL('index');
    }

        protected function afterSave(): void
    {
        $record = $this->record;
        
        // Nếu status thay đổi từ draft/pending sang published và chưa có published_at
        if ($record->status === 'published' && !$record->published_at) {
            $record->update(['published_at' => now()]);
        }
        
        // Nếu status là pending, xóa published_at
        if ($record->status === 'pending') {
            $record->update(['published_at' => null]);
        }
        
        // Emit event để refresh trang client
        $this->dispatch('post-updated');
    }

    protected function afterDelete(): void
    {
        // Emit event để refresh trang client
        $this->dispatch('post-deleted');
    }
}