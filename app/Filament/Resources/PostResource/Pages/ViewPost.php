<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Tiêu đề')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('slug')
                            ->label('Slug'),
                        Infolists\Components\TextEntry::make('excerpt')
                            ->label('Tóm tắt')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('content')
                            ->label('Nội dung')
                            ->html()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Tác giả'),
                        Infolists\Components\TextEntry::make('view_count')
                            ->label('Lượt xem'),
                    ])->columns(2),

                Infolists\Components\Section::make('Media')
                    ->schema([
                        Infolists\Components\ImageEntry::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Trạng thái')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'draft' => 'warning',
                                'published' => 'success',
                                'archived' => 'danger',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'draft' => 'Bản nháp',
                                'published' => 'Đã xuất bản',
                                'archived' => 'Đã lưu trữ',
                            }),
                        Infolists\Components\TextEntry::make('published_at')
                            ->label('Thời gian xuất bản')
                            ->dateTime('d/m/Y H:i'),
                    ])->columns(2),

                Infolists\Components\Section::make('SEO')
                    ->schema([
                        Infolists\Components\TextEntry::make('meta_title')
                            ->label('Meta Title'),
                        Infolists\Components\TextEntry::make('meta_description')
                            ->label('Meta Description')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('meta_keywords')
                            ->label('Meta Keywords'),
                    ])->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Chỉnh sửa'),
        ];
    }
}