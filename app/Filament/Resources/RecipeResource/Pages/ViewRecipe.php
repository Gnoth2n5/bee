<?php

namespace App\Filament\Resources\RecipeResource\Pages;

use App\Filament\Resources\RecipeResource;
use App\Models\Recipe;
use App\Services\RecipeService;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewRecipe extends ViewRecord
{
    protected static string $resource = RecipeResource::class;

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
                        Infolists\Components\TextEntry::make('description')
                            ->label('Mô tả')
                            ->markdown()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('summary')
                            ->label('Tóm tắt')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Tác giả'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'draft' => 'gray',
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'published' => 'info',
                                default => 'gray',
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('Chi tiết công thức')
                    ->schema([
                        Infolists\Components\TextEntry::make('cooking_time')
                            ->label('Thời gian nấu')
                            ->formatStateUsing(fn(int $state): string => $state . ' phút'),
                        Infolists\Components\TextEntry::make('preparation_time')
                            ->label('Thời gian chuẩn bị')
                            ->formatStateUsing(fn(int $state): string => $state . ' phút'),
                        Infolists\Components\TextEntry::make('total_time')
                            ->label('Tổng thời gian')
                            ->formatStateUsing(fn(int $state): string => $state . ' phút'),
                        Infolists\Components\TextEntry::make('difficulty')
                            ->label('Độ khó')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'easy' => 'success',
                                'medium' => 'warning',
                                'hard' => 'danger',
                                'expert' => 'purple',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('servings')
                            ->label('Khẩu phần'),
                        Infolists\Components\TextEntry::make('calories_per_serving')
                            ->label('Calo mỗi khẩu phần')
                            ->formatStateUsing(fn(int $state): string => $state . ' cal'),
                    ])->columns(3),

                Infolists\Components\Section::make('Nguyên liệu')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('ingredients')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Tên nguyên liệu')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('amount')
                                    ->label('Số lượng'),
                                Infolists\Components\TextEntry::make('unit')
                                    ->label('Đơn vị'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Hướng dẫn nấu ăn')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('instructions')
                            ->schema([
                                Infolists\Components\TextEntry::make('step')
                                    ->label('Bước')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('instruction')
                                    ->label('Hướng dẫn')
                                    ->markdown(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Mẹo và ghi chú')
                    ->schema([
                        Infolists\Components\TextEntry::make('tips')
                            ->label('Mẹo nấu ăn')
                            ->markdown()
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Ghi chú')
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Phân loại')
                    ->schema([
                        Infolists\Components\TextEntry::make('categories.name')
                            ->label('Danh mục')
                            ->badge()
                            ->separator(','),
                        Infolists\Components\TextEntry::make('tags.name')
                            ->label('Thẻ')
                            ->badge()
                            ->separator(','),
                    ])->columns(2),

                Infolists\Components\Section::make('Thống kê')
                    ->schema([
                        Infolists\Components\TextEntry::make('view_count')
                            ->label('Lượt xem'),
                        Infolists\Components\TextEntry::make('favorite_count')
                            ->label('Lượt yêu thích'),
                        Infolists\Components\TextEntry::make('rating_count')
                            ->label('Lượt đánh giá'),
                        Infolists\Components\TextEntry::make('average_rating')
                            ->label('Đánh giá trung bình')
                            ->formatStateUsing(fn(float $state): string => number_format($state, 1) . '/5'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tạo lúc')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Cập nhật lúc')
                            ->dateTime('d/m/Y H:i'),
                    ])->columns(3),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $record = $this->record;
        $user = Auth::user();

        $actions = [];

        // Admin luôn có thể chỉnh sửa
        $actions[] = Actions\EditAction::make()
            ->label('Sửa')
            ->icon('heroicon-o-pencil');

        // Admin luôn có thể xóa
        $actions[] = Actions\DeleteAction::make()
            ->label('Xóa công thức')
            ->icon('heroicon-o-trash')
            ->requiresConfirmation();

        // Nếu công thức đang chờ duyệt thì Admin có thể duyệt
        if ($record->status === 'pending') {
            $actions[] = Actions\Action::make('approve')
                ->label('Phê duyệt')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Phê duyệt công thức')
                ->modalDescription('Bạn có chắc chắn muốn phê duyệt công thức này?')
                ->modalSubmitActionLabel('Phê duyệt')
                ->action(function () use ($record, $user) {
                    app(RecipeService::class)->approve($record, $user);

                    Notification::make()
                        ->title('Đã phê duyệt công thức')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                });

            $actions[] = Actions\Action::make('reject')
                ->label('Từ chối')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Lý do từ chối')
                        ->required()
                        ->maxLength(500)
                        ->placeholder('Nhập lý do từ chối công thức (bắt buộc)...'),
                ])
                ->modalHeading('Từ chối công thức')
                ->modalDescription('Vui lòng cung cấp lý do từ chối rõ ràng để tác giả có thể chỉnh sửa.')
                ->modalSubmitActionLabel('Từ chối')
                ->action(function (array $data) use ($record, $user) {
                    app(RecipeService::class)->reject($record, $user, $data['reason']);

                    Notification::make()
                        ->title('Đã từ chối công thức')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                });
        }

        return $actions;
    }
}
