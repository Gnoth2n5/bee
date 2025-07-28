<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Tác giả')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, callable $set) {
                                if (!empty($state)) {
                                    $slug = Str::slug($state);
                                    // Kiểm tra xem slug đã tồn tại chưa
                                    $existingPost = \App\Models\Post::where('slug', $slug)->first();
                                    if ($existingPost) {
                                        $counter = 1;
                                        while (\App\Models\Post::where('slug', $slug . '-' . $counter)->exists()) {
                                            $counter++;
                                        }
                                        $slug = $slug . '-' . $counter;
                                    }
                                    $set('slug', $slug);
                                }
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Tóm tắt')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Nội dung')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('posts')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Trạng thái')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'draft' => 'Bản nháp',
                                'published' => 'Đã xuất bản',
                                'archived' => 'Đã lưu trữ',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Thời gian xuất bản')
                            ->nullable()
                            ->maxDate(now())
                            ->helperText('Không thể set thời gian xuất bản trong tương lai'),
                    ])->columns(2),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(500),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Ảnh')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tác giả')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('excerpt')
                    ->label('Tóm tắt')
                    ->limit(100)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                        'danger' => 'archived',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Bản nháp',
                        'published' => 'Đã xuất bản',
                        'archived' => 'Đã lưu trữ',
                    }),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Lượt xem')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Xuất bản lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Trạng thái xóa'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Bản nháp',
                        'published' => 'Đã xuất bản',
                        'archived' => 'Đã lưu trữ',
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->label('Tác giả')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('publish')
                        ->label('Xuất bản')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->visible(fn(Post $record) => $record->status !== 'published')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'published',
                                'published_at' => now(),
                            ]);
                        }),
                    Tables\Actions\Action::make('archive')
                        ->label('Lưu trữ')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->visible(fn(Post $record) => $record->status !== 'archived')
                        ->action(function (Post $record) {
                            $record->update(['status' => 'archived']);
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa bài viết')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa bài viết')
                        ->modalDescription('Bạn có chắc chắn muốn xóa bài viết này? Bài viết sẽ được chuyển vào thùng rác.')
                        ->modalSubmitActionLabel('Xóa')
                        ->modalCancelActionLabel('Hủy'),
                    Tables\Actions\RestoreAction::make()
                        ->label('Khôi phục')
                        ->color('success'),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Xóa vĩnh viễn')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa vĩnh viễn')
                        ->modalDescription('Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này? Hành động này không thể hoàn tác.')
                        ->modalSubmitActionLabel('Xóa vĩnh viễn')
                        ->modalCancelActionLabel('Hủy')
                        ->color('danger'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish_selected')
                        ->label('Xuất bản đã chọn')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Xuất bản bài viết đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn xuất bản tất cả bài viết đã chọn?')
                        ->modalSubmitActionLabel('Xuất bản')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'published') {
                                    $record->update([
                                        'status' => 'published',
                                        'published_at' => now(),
                                    ]);
                                    $count++;
                                }
                            }
                            return $count . ' bài viết đã được xuất bản.';
                        }),
                    Tables\Actions\BulkAction::make('archive_selected')
                        ->label('Lưu trữ đã chọn')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Lưu trữ bài viết đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn lưu trữ tất cả bài viết đã chọn?')
                        ->modalSubmitActionLabel('Lưu trữ')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'archived') {
                                    $record->update(['status' => 'archived']);
                                    $count++;
                                }
                            }
                            return $count . ' bài viết đã được lưu trữ.';
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa bài viết đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn xóa tất cả bài viết đã chọn? Hành động này không thể hoàn tác.')
                        ->modalSubmitActionLabel('Xóa')
                        ->modalCancelActionLabel('Hủy'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user']);
    }
}