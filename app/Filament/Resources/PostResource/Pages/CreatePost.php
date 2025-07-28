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
        // Tự động set published_at nếu status là published
        $record = $this->record;
        if ($record->status === 'published' && !$record->published_at) {
            $record->update(['published_at' => now()]);
        }

        // Nếu published_at trong tương lai, set về hiện tại
        if ($record->published_at && $record->published_at->isFuture()) {
            $record->update(['published_at' => now()]);
        }

        // Emit event để refresh trang client
        $this->dispatch('post-created');
    }
}