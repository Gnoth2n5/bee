<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VipPostApprovalResource\Pages;
use App\Models\Post;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;

/**
 * Resource để Admin duyệt bài viết của VIP users
 * 
 * Chức năng tương tự ManagerResources\VipPostApprovalResource
 * nhưng dành cho Admin panel
 */
class VipPostApprovalResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'vip-post-approvals';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Duyệt bài viết VIP';

    protected static ?string $navigationGroup = 'Quản lý phê duyệt';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        // Chỉ cho phép Admin
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return Auth::check() && $user->hasRole('admin');
    }

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = static::getModel()::where('status', 'pending')->count();
        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Form chỉ để xem, không cho phép chỉnh sửa
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin bài viết')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Tác giả VIP')
                            ->disabled(),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->disabled(),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Tóm tắt')
                            ->disabled()
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Nội dung')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Nội dung bài viết')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Thông tin khác')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->disabled(),
                        Forms\Components\TextInput::make('view_count')
                            ->label('Lượt xem')
                            ->disabled()
                            ->numeric(),
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'draft' => 'Bản nháp',
                                'pending' => 'Chờ duyệt',
                                'published' => 'Đã xuất bản',
                                'archived' => 'Đã lưu trữ',
                            ])
                            ->disabled(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->disabled()
                            ->visible(fn($record) => !empty($record?->rejection_reason))
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Ngày tạo')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Cập nhật lần cuối')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Ngày xuất bản')
                            ->disabled(),
                    ])->columns(3)
                    ->collapsible(),
            ]);
    }

    /**
     * Bảng hiển thị bài viết VIP chờ duyệt
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
                    ->label('Tác giả VIP')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
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
                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Lý do từ chối')
                    ->limit(50)
                    ->tooltip(fn($state) => $state)
                    ->color('danger')
                    ->visible(fn(?Post $record): bool => $record && !empty($record->rejection_reason))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ duyệt',
                        'published' => 'Đã xuất bản',
                        'archived' => 'Đã lưu trữ',
                        'draft' => 'Bản nháp (có lý do từ chối)',
                    ])
                    ->default('pending'),
                Tables\Filters\SelectFilter::make('user')
                    ->label('Tác giả VIP')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Xem chi tiết'),

                    // ACTION CHÍNH: Duyệt bài viết
                    Tables\Actions\Action::make('approve')
                        ->label('Duyệt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Duyệt bài viết VIP')
                        ->modalDescription('Bạn có chắc chắn muốn duyệt và xuất bản bài viết này?')
                        ->modalSubmitActionLabel('Duyệt & Xuất bản')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'published',
                                'published_at' => now(),
                                'rejection_reason' => null, // Xóa lý do từ chối cũ
                            ]);
                        })
                        ->visible(fn(Post $record) => in_array($record->status, ['pending', 'draft'])),

                    // ACTION: Từ chối bài viết
                    Tables\Actions\Action::make('reject')
                        ->label('Từ chối')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Lý do từ chối')
                                ->required()
                                ->rows(3)
                                ->helperText('Lý do sẽ được hiển thị cho tác giả VIP'),
                        ])
                        ->requiresConfirmation()
                        ->modalHeading('Từ chối bài viết VIP')
                        ->modalSubmitActionLabel('Từ chối')
                        ->action(function (Post $record, array $data) {
                            $record->update([
                                'status' => 'draft',
                                'rejection_reason' => $data['rejection_reason'],
                            ]);

                            // TODO: Gửi notification cho tác giả VIP
                        })
                        ->visible(fn(Post $record) => $record->status === 'pending'),

                    // ACTION: Lưu trữ bài viết
                    Tables\Actions\Action::make('archive')
                        ->label('Lưu trữ')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Lưu trữ bài viết VIP')
                        ->modalDescription('Bài viết sẽ được ẩn khỏi trang web')
                        ->modalSubmitActionLabel('Lưu trữ')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'archived',
                            ]);
                        })
                        ->visible(fn(Post $record) => $record->status === 'published'),

                    // ACTION: Khôi phục từ lưu trữ
                    Tables\Actions\Action::make('unarchive')
                        ->label('Khôi phục')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Khôi phục bài viết VIP')
                        ->modalDescription('Bài viết sẽ được xuất bản lại')
                        ->modalSubmitActionLabel('Khôi phục')
                        ->action(function (Post $record) {
                            $record->update([
                                'status' => 'published',
                                'published_at' => $record->published_at ?? now(),
                            ]);
                        })
                        ->visible(fn(Post $record) => $record->status === 'archived'),
                ])
                    ->label('Thao tác')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray')
                    ->button()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Duyệt đã chọn')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Duyệt bài viết VIP đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn duyệt và xuất bản tất cả bài viết đã chọn?')
                        ->modalSubmitActionLabel('Duyệt tất cả')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending', 'draft'])) {
                                    $record->update([
                                        'status' => 'published',
                                        'published_at' => now(),
                                        'rejection_reason' => null,
                                    ]);
                                    $count++;
                                }
                            }
                            return "Đã duyệt {$count} bài viết VIP.";
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('archive_selected')
                        ->label('Lưu trữ đã chọn')
                        ->icon('heroicon-o-archive-box')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Lưu trữ bài viết VIP đã chọn')
                        ->modalDescription('Tất cả bài viết đã chọn sẽ được ẩn khỏi trang web')
                        ->modalSubmitActionLabel('Lưu trữ tất cả')
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'published') {
                                    $record->update(['status' => 'archived']);
                                    $count++;
                                }
                            }
                            return "Đã lưu trữ {$count} bài viết VIP.";
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Không có bài viết VIP nào')
            ->emptyStateDescription('Chưa có bài viết nào từ VIP users cần xử lý.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Thông tin bài viết')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('Tiêu đề')
                            ->size('lg')
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Tác giả VIP')
                            ->badge()
                            ->color('success'),
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
                            }),
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

                Infolists\Components\Section::make('Lý do từ chối')
                    ->schema([
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->color('danger')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn($record) => !empty($record->rejection_reason))
                    ->collapsible(),

                Infolists\Components\Section::make('Thông tin khác')
                    ->schema([
                        Infolists\Components\TextEntry::make('view_count')
                            ->label('Lượt xem')
                            ->numeric(),
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
                    ])->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVipPostApprovals::route('/'),
            'view' => Pages\ViewVipPostApproval::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user', function (Builder $query) {
                // Chỉ hiển thị bài viết của VIP users
                $query->whereHas('profile', function (Builder $subQuery) {
                    $subQuery->where('isVipAccount', true);
                });
            })
            ->with(['user', 'user.profile']);
    }

    /**
     * Admin không thể tạo bài viết qua resource này
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Admin không thể chỉnh sửa bài viết qua resource này
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    /**
     * Admin không thể xóa bài viết qua resource này
     */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    /**
     * Admin có thể xem tất cả bài viết VIP
     */
    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }
}
