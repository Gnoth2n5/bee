<?php

namespace App\Filament\ManagerResources;

use App\Filament\ManagerResources\PostResource\Pages;
use App\Models\Post;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Resource quản lý bài viết dành cho Manager
 * 
 * Resource này cung cấp đầy đủ chức năng CRUD bài viết cho Manager:
 * - Tạo, sửa, xóa bài viết
 * - Xuất bản và quản lý trạng thái bài viết
 * - Tối ưu hóa giao diện cho Manager
 */
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Quản lý bài viết';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    /**
     * Form tối ưu cho Manager với các trường cần thiết
     */
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
                            ->required()
                            ->default(Auth::id()),
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, callable $set) {
                                if (!empty($state)) {
                                    $slug = Str::slug($state);
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
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Nội dung')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Nội dung bài viết')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'heading',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                            ]),
                    ]),

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

                Forms\Components\Section::make('Trạng thái và xuất bản')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'draft' => 'Bản nháp',
                                'pending' => 'Chờ duyệt',
                                'published' => 'Đã xuất bản',
                                'archived' => 'Đã lưu trữ',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Thời gian xuất bản')
                            ->nullable()
                            ->helperText('Để trống để xuất bản ngay, hoặc chọn thời gian trong tương lai để đặt lịch'),
                    ])->columns(2),

                Forms\Components\Section::make('SEO')
                    ->description('Tối ưu hóa công cụ tìm kiếm')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255)
                            ->placeholder('Tiêu đề SEO (tùy chọn)'),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Mô tả ngắn cho SEO (tùy chọn)')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(500)
                            ->placeholder('Từ khóa SEO (tùy chọn)'),
                    ])->columns(2)
                    ->collapsible(),
            ]);
    }

    /**
     * Bảng được tối ưu cho Manager
     */
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
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Xuất bản lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Bản nháp',
                        'pending' => 'Chờ duyệt',
                        'published' => 'Đã xuất bản',
                        'archived' => 'Đã lưu trữ',
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->label('Tác giả')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('created_today')
                    ->label('Tạo hôm nay')
                    ->query(fn(Builder $query): Builder => $query->whereDate('created_at', today())),
                Tables\Filters\Filter::make('published_this_week')
                    ->label('Xuất bản tuần này')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Xem chi tiết'),
                    Tables\Actions\EditAction::make()
                        ->label('Chỉnh sửa'),

                    // ACTION CHÍNH: Xuất bản bài viết
                    Tables\Actions\Action::make('publish')
                        ->label('Xuất bản')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Xuất bản bài viết')
                        ->modalDescription('Bạn có chắc chắn muốn xuất bản bài viết này?')
                        ->modalSubmitActionLabel('Xuất bản')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'published',
                                'published_at' => now(),
                            ]);
                        })
                        ->visible(fn(Post $record) => $record->status !== 'published'),

                    // ACTION: Lưu trữ bài viết
                    Tables\Actions\Action::make('archive')
                        ->label('Lưu trữ')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Lưu trữ bài viết')
                        ->modalDescription('Bài viết sẽ không hiển thị công khai sau khi lưu trữ.')
                        ->modalSubmitActionLabel('Lưu trữ')
                        ->action(function (Post $record) {
                            $record->update(['status' => 'archived']);
                        })
                        ->visible(fn(Post $record) => $record->status !== 'archived'),

                    // ACTION: Chuyển về bản nháp
                    Tables\Actions\Action::make('to_draft')
                        ->label('Chuyển về nháp')
                        ->icon('heroicon-o-document')
                        ->color('gray')
                        ->action(function (Post $record) {
                            $record->update(['status' => 'draft']);
                        })
                        ->visible(fn(Post $record) => in_array($record->status, ['published', 'archived'])),

                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa bài viết')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa bài viết')
                        ->modalDescription('Bài viết sẽ được chuyển vào thùng rác và có thể khôi phục.')
                        ->modalSubmitActionLabel('Xóa'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // BULK ACTION: Xuất bản hàng loạt
                    Tables\Actions\BulkAction::make('publish_selected')
                        ->label('Xuất bản đã chọn')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Xuất bản bài viết đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn xuất bản tất cả bài viết đã chọn?')
                        ->modalSubmitActionLabel('Xuất bản tất cả')
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
                            \Filament\Notifications\Notification::make()
                                ->title("Đã xuất bản {$count} bài viết")
                                ->success()
                                ->send();
                        }),

                    // BULK ACTION: Lưu trữ hàng loạt
                    Tables\Actions\BulkAction::make('archive_selected')
                        ->label('Lưu trữ đã chọn')
                        ->icon('heroicon-o-archive-box')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Lưu trữ bài viết đã chọn')
                        ->modalDescription('Các bài viết sẽ không hiển thị công khai sau khi lưu trữ.')
                        ->modalSubmitActionLabel('Lưu trữ tất cả')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'archived') {
                                    $record->update(['status' => 'archived']);
                                    $count++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title("Đã lưu trữ {$count} bài viết")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa bài viết đã chọn')
                        ->modalDescription('Các bài viết sẽ được chuyển vào thùng rác.')
                        ->modalSubmitActionLabel('Xóa tất cả'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s'); // Tự động làm mới mỗi phút
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
            ->where('user_id', Auth::id()) // Chỉ hiển thị bài viết của chính Manager
            ->with(['user']);
    }

    /**
     * Manager chỉ có thể chỉnh sửa bài viết của chính mình
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }

    /**
     * Manager chỉ có thể xóa bài viết của chính mình
     */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }

    /**
     * Manager chỉ có thể xem bài viết của chính mình
     */
    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }
}
