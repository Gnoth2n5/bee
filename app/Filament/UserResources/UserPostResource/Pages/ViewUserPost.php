<?php

namespace App\Filament\UserResources\UserPostResource\Pages;

use App\Filament\UserResources\UserPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewUserPost extends ViewRecord
{
    protected static string $resource = UserPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Chỉnh sửa'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Thông tin bài viết')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Tiêu đề')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('slug')
                            ->label('Slug')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'draft' => 'gray',
                                'pending' => 'warning',
                                'published' => 'success',
                                'archived' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'draft' => 'Bản nháp',
                                'pending' => 'Chờ duyệt',
                                'published' => 'Đã xuất bản',
                                'archived' => 'Đã lưu trữ',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('view_count')
                            ->label('Lượt xem')
                            ->numeric(),
                    ])->columns(2),

                Infolists\Components\Section::make('Nội dung')
                    ->schema([
                        Infolists\Components\ImageEntry::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->size(200),
                        Infolists\Components\TextEntry::make('excerpt')
                            ->label('Tóm tắt')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('content')
                            ->label('Nội dung')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('SEO')
                    ->schema([
                        Infolists\Components\TextEntry::make('meta_title')
                            ->label('Tiêu đề SEO'),
                        Infolists\Components\TextEntry::make('meta_description')
                            ->label('Mô tả SEO'),
                        Infolists\Components\TextEntry::make('meta_keywords')
                            ->label('Từ khóa SEO'),
                    ])->columns(1)
                    ->collapsible(),

                Infolists\Components\Section::make('Thông tin khác')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Cập nhật lần cuối')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('published_at')
                            ->label('Ngày xuất bản')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Chưa xuất bản'),
                    ])->columns(3)
                    ->collapsible(),
            ]);
    }

    public function getTitle(): string
    {
        return 'Chi tiết bài viết';
    }
}
