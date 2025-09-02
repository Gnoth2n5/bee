<?php

namespace App\Filament\UserResources\UserPostResource\Pages;

use App\Filament\UserResources\UserPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUserPosts extends ListRecords
{
    protected static string $resource = UserPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo bài viết mới')
                ->icon('heroicon-o-plus')
                ->mutateFormDataUsing(function (array $data): array {
                    // Tự động gán user_id cho bài viết mới
                    $data['user_id'] = Auth::id();
                    return $data;
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Có thể thêm widgets thống kê sau
        ];
    }

    public function getTitle(): string
    {
        return 'Bài viết của tôi';
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tạo bài viết mới')
                ->icon('heroicon-o-plus'),
        ];
    }
}
