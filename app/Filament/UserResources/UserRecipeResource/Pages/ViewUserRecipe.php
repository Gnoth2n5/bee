<?php

namespace App\Filament\UserResources\UserRecipeResource\Pages;

use App\Filament\UserResources\UserRecipeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewUserRecipe extends ViewRecord
{
    protected static string $resource = UserRecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Sửa công thức')
                ->icon('heroicon-o-pencil'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin cơ bản')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Tiêu đề')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        TextEntry::make('description')
                            ->label('Mô tả')
                            ->markdown(),
                        TextEntry::make('summary')
                            ->label('Tóm tắt'),
                        ImageEntry::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->circular()
                            ->size(200),
                    ])->columns(2),

                Section::make('Chi tiết công thức')
                    ->schema([
                        TextEntry::make('cooking_time')
                            ->label('Thời gian nấu')
                            ->suffix(' phút'),
                        TextEntry::make('preparation_time')
                            ->label('Thời gian chuẩn bị')
                            ->suffix(' phút'),
                        TextEntry::make('total_time')
                            ->label('Tổng thời gian')
                            ->suffix(' phút'),
                        TextEntry::make('servings')
                            ->label('Khẩu phần'),
                        TextEntry::make('calories_per_serving')
                            ->label('Calo mỗi khẩu phần')
                            ->suffix(' kcal'),
                        TextEntry::make('difficulty')
                            ->label('Độ khó')
                            ->badge()
                            ->formatStateUsing(function (string $state): string {
                                return match ($state) {
                                    'easy' => 'Dễ',
                                    'medium' => 'Trung bình',
                                    'hard' => 'Khó',
                                    default => $state,
                                };
                            }),
                    ])->columns(3),

                Section::make('Nguyên liệu')
                    ->schema([
                        RepeatableEntry::make('ingredients')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Tên nguyên liệu')
                                    ->weight('bold'),
                                TextEntry::make('amount')
                                    ->label('Số lượng'),
                                TextEntry::make('unit')
                                    ->label('Đơn vị'),
                            ])
                            ->columns(3),
                    ]),

                Section::make('Hướng dẫn nấu')
                    ->schema([
                        RepeatableEntry::make('instructions')
                            ->schema([
                                TextEntry::make('step')
                                    ->label('Bước')
                                    ->weight('bold'),
                                TextEntry::make('instruction')
                                    ->label('Hướng dẫn')
                                    ->markdown(),
                            ])
                            ->columns(2),
                    ]),

                Section::make('Phân loại')
                    ->schema([
                        TextEntry::make('categories.name')
                            ->label('Danh mục')
                            ->badge()
                            ->separator(','),
                        TextEntry::make('tags.name')
                            ->label('Thẻ')
                            ->badge()
                            ->separator(','),
                    ])->columns(2),

                Section::make('Mẹo và ghi chú')
                    ->schema([
                        TextEntry::make('tips')
                            ->label('Mẹo nấu ăn')
                            ->markdown(),
                        TextEntry::make('notes')
                            ->label('Ghi chú')
                            ->markdown(),
                    ])->columns(2),

                Section::make('Trạng thái phê duyệt')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->formatStateUsing(function (string $state): string {
                                return match ($state) {
                                    'draft' => 'Bản nháp',
                                    'pending' => 'Đang chờ duyệt',
                                    'approved' => 'Đã được duyệt',
                                    'rejected' => 'Bị từ chối',
                                    'published' => 'Đã xuất bản',
                                    default => $state,
                                };
                            })
                            ->color(function (string $state): string {
                                return match ($state) {
                                    'draft' => 'gray',
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'published' => 'info',
                                    default => 'gray',
                                };
                            }),
                        TextEntry::make('approver.name')
                            ->label('Người phê duyệt')
                            ->visible(fn($record): bool => in_array($record->status, ['approved', 'rejected'])),
                        TextEntry::make('approved_at')
                            ->label('Thời gian phê duyệt')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn($record): bool => in_array($record->status, ['approved', 'rejected'])),
                        TextEntry::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->visible(fn($record): bool => $record->status === 'rejected')
                            ->color('danger'),
                    ])->columns(2),

                Section::make('Thông tin khác')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Ngày cập nhật')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('view_count')
                            ->label('Lượt xem'),
                        TextEntry::make('favorite_count')
                            ->label('Lượt yêu thích'),
                        TextEntry::make('rating_count')
                            ->label('Lượt đánh giá'),
                        TextEntry::make('average_rating')
                            ->label('Điểm đánh giá trung bình')
                            ->suffix('/ 5'),
                    ])->columns(3),
            ]);
    }
}