<?php

namespace App\Filament\UserResources;

use App\Filament\UserResources\UserPostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * Resource quản lý bài viết dành cho User VIP
 * 
 * Resource này cung cấp chức năng CRUD bài viết cho User VIP:
 * - Tạo, sửa, xóa bài viết cá nhân
 * - Gửi bài viết để chờ duyệt
 * - Xem trạng thái phê duyệt
 */
class UserPostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Bài viết của tôi';

    protected static ?string $navigationGroup = 'Quản lý cá nhân';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        // Chỉ cho phép user VIP
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return Auth::check() && $user->isVip();
    }

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = static::getModel()::where('user_id', Auth::id())
            ->where('status', 'pending')->count();
        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Form tối ưu cho User VIP
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
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
                            ->label('Slug (URL thân thiện)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Sẽ tự động tạo từ tiêu đề nếu để trống'),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Tóm tắt')
                            ->maxLength(500)
                            ->rows(3)
                            ->helperText('Mô tả ngắn gọn về nội dung bài viết')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Nội dung')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Nội dung bài viết')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    ]),

                Forms\Components\Section::make('Hình ảnh & SEO')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->image()
                            ->directory('posts')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Tiêu đề SEO')
                            ->maxLength(60)
                            ->helperText('Tối đa 60 ký tự cho SEO tốt'),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Mô tả SEO')
                            ->maxLength(160)
                            ->rows(2)
                            ->helperText('Tối đa 160 ký tự cho SEO tốt'),
                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Từ khóa SEO')
                            ->helperText('Phân cách bằng dấu phẩy'),
                    ])->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Trạng thái')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'draft' => 'Bản nháp',
                                'pending' => 'Chờ duyệt',
                            ])
                            ->default('draft')
                            ->required()
                            ->helperText('Chọn "Chờ duyệt" để gửi bài viết cho admin phê duyệt'),
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Ngày tạo')
                            ->content(fn(Post $record): ?string => $record?->created_at?->format('d/m/Y H:i')),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Cập nhật lần cuối')
                            ->content(fn(Post $record): ?string => $record?->updated_at?->format('d/m/Y H:i')),
                    ])->columns(3)
                    ->collapsible(),
            ]);
    }

    /**
     * Bảng được tối ưu cho User VIP
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
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Ngày xuất bản')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y')
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
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Xem chi tiết'),
                    Tables\Actions\EditAction::make()
                        ->label('Chỉnh sửa'),

                    // ACTION: Gửi để duyệt
                    Tables\Actions\Action::make('submit_for_approval')
                        ->label('Gửi duyệt')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Gửi bài viết để duyệt')
                        ->modalDescription('Bạn có chắc chắn muốn gửi bài viết này để admin phê duyệt?')
                        ->modalSubmitActionLabel('Gửi duyệt')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'pending',
                            ]);
                        })
                        ->visible(fn(Post $record) => $record->status === 'draft'),

                    // ACTION: Rút lại yêu cầu duyệt
                    Tables\Actions\Action::make('withdraw_approval')
                        ->label('Rút lại')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Rút lại yêu cầu duyệt')
                        ->modalDescription('Bạn có chắc chắn muốn rút lại yêu cầu duyệt bài viết này?')
                        ->modalSubmitActionLabel('Rút lại')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'draft',
                            ]);
                        })
                        ->visible(fn(Post $record) => $record->status === 'pending'),

                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa'),
                ])
                    ->label('Thao tác')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('submit_selected_for_approval')
                        ->label('Gửi duyệt đã chọn')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Gửi bài viết đã chọn để duyệt')
                        ->modalDescription('Bạn có chắc chắn muốn gửi tất cả bài viết đã chọn để admin phê duyệt?')
                        ->modalSubmitActionLabel('Gửi duyệt')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'draft') {
                                    $record->update(['status' => 'pending']);
                                    $count++;
                                }
                            }
                            return "Đã gửi {$count} bài viết để phê duyệt.";
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tạo bài viết đầu tiên')
                    ->icon('heroicon-o-plus'),
            ])
            ->emptyStateHeading('Chưa có bài viết nào')
            ->emptyStateDescription('Tạo bài viết đầu tiên để chia sẻ nội dung của bạn với cộng đồng.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserPosts::route('/'),
            'create' => Pages\CreateUserPost::route('/create'),
            'view' => Pages\ViewUserPost::route('/{record}'),
            'edit' => Pages\EditUserPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id()) // Chỉ hiển thị bài viết của chính user
            ->with(['user']);
    }

    /**
     * User chỉ có thể chỉnh sửa bài viết của chính mình
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }

    /**
     * User chỉ có thể xóa bài viết của chính mình
     */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }

    /**
     * User chỉ có thể xem bài viết của chính mình
     */
    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }
}
