<?php

namespace App\Filament\ManagerWidgets;

use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

/**
 * Widget hiển thị bài viết của chính Manager
 * 
 * Widget này chỉ hiển thị bài viết do Manager tạo
 */
class MyRecentPosts extends BaseWidget
{
    protected static ?string $heading = 'Bài viết của tôi';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->where('user_id', Auth::id()) // Chỉ bài viết của chính Manager
                    ->with(['user'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Ảnh')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(60)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
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
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Lượt xem')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('quick_publish')
                        ->label('Xuất bản')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->size('sm')
                        ->requiresConfirmation()
                        ->modalHeading('Xuất bản bài viết')
                        ->modalDescription('Xuất bản bài viết này ngay lập tức?')
                        ->modalSubmitActionLabel('Xuất bản')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'published',
                                'published_at' => now(),
                            ]);
                        })
                        ->visible(fn(Post $record) => $record->status !== 'published'),
                    Tables\Actions\EditAction::make('edit')
                        ->label('Sửa')
                        ->icon('heroicon-o-pencil')
                        ->size('sm')
                        ->url(fn(Post $record): string => route('filament.manager.resources.posts.edit', $record)),
                    Tables\Actions\ViewAction::make('view')
                        ->label('Xem')
                        ->icon('heroicon-o-eye')
                        ->size('sm')
                        ->url(fn(Post $record): string => route('filament.manager.resources.posts.view', $record)),
                ])
            ])
            ->defaultPaginationPageOption(5)
            ->poll('60s')
            ->emptyStateHeading('Chưa có bài viết nào')
            ->emptyStateDescription('Hãy tạo bài viết đầu tiên của bạn.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateActions([
                Tables\Actions\Action::make('create_post')
                    ->label('Tạo bài viết mới')
                    ->icon('heroicon-o-plus')
                    ->url(route('filament.manager.resources.posts.create'))
                    ->color('primary'),
            ]);
    }
}
