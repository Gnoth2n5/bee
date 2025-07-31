<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        // Nếu status là published và chưa có published_at, set về hiện tại
        if ($record->status === 'published' && !$record->published_at) {
            $record->update(['published_at' => now()]);
        }
        
        // Nếu status là pending, xóa published_at (sẽ được set khi admin approve)
        if ($record->status === 'pending') {
            $record->update(['published_at' => null]);
        }

        // Emit event để refresh trang client
        $this->dispatch('post-created');
    }
}