<?php

namespace App\Filament\ManagerResources\PostResource\Pages;

use App\Filament\ManagerResources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Chỉnh sửa'),
        ];
    }
}
