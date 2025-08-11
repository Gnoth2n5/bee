<?php

namespace App\Filament\ManagerResources;

use App\Filament\ManagerResources\RecipeResource\Pages;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * Resource quản lý công thức dành cho Manager
 * 
 * Resource này cung cấp đầy đủ chức năng quản lý công thức cho Manager:
 * - Xem danh sách công thức với các bộ lọc
 * - Duyệt/từ chối công thức (action chính của Manager)
 * - Chỉnh sửa thông tin công thức
 * - Hành động hàng loạt cho việc duyệt
 */
class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationLabel = 'Quản lý công thức';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /**
     * Hiển thị chi tiết công thức với đầy đủ thông tin
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Thông báo trạng thái duyệt
                Infolists\Components\Section::make('')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Trạng thái phê duyệt')
                            ->formatStateUsing(function ($record) {
                                if ($record->user_id !== Auth::id() && $record->status === 'pending') {
                                    return '⏰ Công thức này đang chờ phê duyệt từ bạn. Sử dụng các nút ở header để duyệt/từ chối.';
                                } elseif ($record->user_id === Auth::id()) {
                                    return '👤 Đây là công thức của bạn. Bạn có thể chỉnh sửa hoặc xóa.';
                                } elseif ($record->status === 'approved') {
                                    $approverName = 'N/A';
                                    if ($record->approved_by) {
                                        $approver = User::find($record->approved_by);
                                        $approverName = $approver?->name ?? 'N/A';
                                    }
                                    return '✅ Công thức đã được phê duyệt bởi ' . $approverName;
                                } elseif ($record->status === 'rejected') {
                                    return '❌ Công thức đã bị từ chối';
                                }
                                return '';
                            })
                            ->color(function ($record) {
                                if ($record->status === 'pending') return 'warning';
                                if ($record->status === 'approved') return 'success';
                                if ($record->status === 'rejected') return 'danger';
                                return 'info';
                            })
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),
                    ])
                    ->visible(
                        fn($record) => ($record->user_id !== Auth::id() && $record->status === 'pending') ||
                            ($record->user_id === Auth::id()) ||
                            in_array($record->status, ['approved', 'rejected'])
                    )
                    ->compact(),

                Infolists\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Tiêu đề')
                                    ->size('lg')
                                    ->weight('bold'),
                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('Tác giả')
                                    ->badge()
                                    ->color('info'),
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
                                Infolists\Components\TextEntry::make('difficulty')
                                    ->label('Độ khó')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'easy' => 'success',
                                        'medium' => 'warning',
                                        'hard' => 'danger',
                                        'expert' => 'purple',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                        'easy' => 'Dễ',
                                        'medium' => 'Trung bình',
                                        'hard' => 'Khó',
                                        'expert' => 'Chuyên gia',
                                        default => $state,
                                    }),
                            ]),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Mô tả')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('summary')
                            ->label('Tóm tắt')
                            ->columnSpanFull()
                            ->placeholder('Chưa có tóm tắt'),
                    ]),

                Infolists\Components\Section::make('Chi tiết nấu ăn')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('cooking_time')
                                    ->label('Thời gian nấu')
                                    ->suffix(' phút')
                                    ->placeholder('N/A'),
                                Infolists\Components\TextEntry::make('preparation_time')
                                    ->label('Thời gian chuẩn bị')
                                    ->suffix(' phút')
                                    ->placeholder('N/A'),
                                Infolists\Components\TextEntry::make('servings')
                                    ->label('Khẩu phần')
                                    ->suffix(' người'),
                                Infolists\Components\TextEntry::make('calories_per_serving')
                                    ->label('Calo/khẩu phần')
                                    ->suffix(' kcal')
                                    ->placeholder('N/A'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Media')
                    ->schema([
                        Infolists\Components\ImageEntry::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->size(300)
                            ->placeholder('Chưa có ảnh'),
                        Infolists\Components\TextEntry::make('video_url')
                            ->label('Video URL')
                            ->url(fn($state) => $state)
                            ->openUrlInNewTab()
                            ->placeholder('Chưa có video'),
                    ])->columns(2),

                Infolists\Components\Section::make('Nguyên liệu')
                    ->schema([
                        Infolists\Components\TextEntry::make('ingredients')
                            ->label('Danh sách nguyên liệu')
                            ->formatStateUsing(function ($state) {
                                if (!$state || !is_array($state)) {
                                    return 'Chưa có nguyên liệu';
                                }

                                $output = '';
                                foreach ($state as $ingredient) {
                                    if (!is_array($ingredient)) continue;

                                    $name = $ingredient['name'] ?? 'N/A';
                                    $amount = $ingredient['amount'] ?? '';
                                    $unit = $ingredient['unit'] ?? '';

                                    $line = "• **{$name}**";
                                    if ($amount) {
                                        $line .= ": {$amount}";
                                        if ($unit) {
                                            $line .= " {$unit}";
                                        }
                                    }
                                    $output .= $line . "\n";
                                }
                                return trim($output);
                            })
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Hướng dẫn nấu ăn')
                    ->schema([
                        Infolists\Components\TextEntry::make('instructions')
                            ->label('Các bước thực hiện')
                            ->formatStateUsing(function ($state) {
                                if (!$state || !is_array($state)) {
                                    return 'Chưa có hướng dẫn';
                                }

                                $output = '';
                                foreach ($state as $index => $instruction) {
                                    $stepNumber = $index + 1;
                                    $content = '';

                                    if (is_array($instruction)) {
                                        $content = $instruction['instruction'] ?? '';
                                    } elseif (is_string($instruction)) {
                                        $content = $instruction;
                                    }

                                    if ($content) {
                                        $output .= "**Bước {$stepNumber}:** {$content}\n\n";
                                    }
                                }
                                return trim($output);
                            })
                            ->markdown()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Mẹo và ghi chú')
                    ->schema([
                        Infolists\Components\TextEntry::make('tips')
                            ->label('Mẹo nấu ăn')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Chưa có mẹo nấu ăn'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Ghi chú')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('Chưa có ghi chú'),
                    ]),

                Infolists\Components\Section::make('Phân loại')
                    ->schema([
                        Infolists\Components\TextEntry::make('categories.name')
                            ->label('Danh mục')
                            ->badge()
                            ->separator(',')
                            ->placeholder('Chưa phân loại'),
                        Infolists\Components\TextEntry::make('tags.name')
                            ->label('Thẻ')
                            ->badge()
                            ->separator(',')
                            ->placeholder('Chưa có thẻ'),
                    ])->columns(2),

                Infolists\Components\Section::make('Thông tin phê duyệt')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('approved_by')
                                    ->label('Phê duyệt bởi')
                                    ->formatStateUsing(fn($state) => $state ? User::find($state)?->name : 'Chưa duyệt')
                                    ->placeholder('Chưa duyệt'),
                                Infolists\Components\TextEntry::make('approved_at')
                                    ->label('Thời gian phê duyệt')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Chưa duyệt'),
                                Infolists\Components\TextEntry::make('rejection_reason')
                                    ->label('Lý do từ chối')
                                    ->color('danger')
                                    ->columnSpanFull()
                                    ->placeholder('Không có')
                                    ->visible(fn($record) => $record->status === 'rejected'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Thống kê')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('view_count')
                                    ->label('Lượt xem')
                                    ->numeric(),
                                Infolists\Components\TextEntry::make('favorite_count')
                                    ->label('Yêu thích')
                                    ->numeric(),
                                Infolists\Components\TextEntry::make('rating_count')
                                    ->label('Đánh giá')
                                    ->numeric(),
                                Infolists\Components\TextEntry::make('average_rating')
                                    ->label('Điểm TB')
                                    ->numeric(
                                        decimalPlaces: 1,
                                    )
                                    ->suffix('/5'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Thời gian')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Tạo lúc')
                                    ->dateTime('d/m/Y H:i'),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('Cập nhật lúc')
                                    ->dateTime('d/m/Y H:i'),
                                Infolists\Components\TextEntry::make('published_at')
                                    ->label('Xuất bản lúc')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Chưa xuất bản'),
                            ]),
                    ])->collapsible(),
            ]);
    }

    /**
     * Form được tối ưu cho Manager - chỉ hiển thị các trường cần thiết
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
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $state, callable $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Chi tiết công thức')
                    ->schema([
                        Forms\Components\TextInput::make('cooking_time')
                            ->label('Thời gian nấu (phút)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('preparation_time')
                            ->label('Thời gian chuẩn bị (phút)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Select::make('difficulty')
                            ->label('Độ khó')
                            ->options([
                                'easy' => 'Dễ',
                                'medium' => 'Trung bình',
                                'hard' => 'Khó',
                                'expert' => 'Chuyên gia',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('servings')
                            ->label('Khẩu phần')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1),
                    ])->columns(4),

                Forms\Components\Section::make('Trạng thái và phê duyệt')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'draft' => 'Bản nháp',
                                'pending' => 'Chờ phê duyệt',
                                'approved' => 'Đã phê duyệt',
                                'rejected' => 'Từ chối',
                                'published' => 'Đã xuất bản',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Select::make('approved_by')
                            ->label('Phê duyệt bởi')
                            ->options(User::whereHas('roles', function ($query) {
                                $query->whereIn('name', ['admin', 'manager']);
                            })->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Thời gian phê duyệt'),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Lý do từ chối')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    /**
     * Bảng được tối ưu cho Manager - tập trung vào việc duyệt công thức
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'published' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Bản nháp',
                        'pending' => 'Chờ phê duyệt',
                        'approved' => 'Đã phê duyệt',
                        'rejected' => 'Từ chối',
                        'published' => 'Đã xuất bản',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('difficulty')
                    ->label('Độ khó')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'easy' => 'success',
                        'medium' => 'warning',
                        'hard' => 'danger',
                        'expert' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'easy' => 'Dễ',
                        'medium' => 'Trung bình',
                        'hard' => 'Khó',
                        'expert' => 'Chuyên gia',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('cooking_time')
                    ->label('Thời gian nấu')
                    ->formatStateUsing(fn(?int $state): string => $state ? $state . ' phút' : 'N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Duyệt lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'draft' => 'Bản nháp',
                        'pending' => 'Chờ phê duyệt',
                        'approved' => 'Đã phê duyệt',
                        'rejected' => 'Từ chối',
                        'published' => 'Đã xuất bản',
                    ])
                    ->default('pending'), // Mặc định hiển thị công thức chờ duyệt
                Tables\Filters\SelectFilter::make('difficulty')
                    ->label('Độ khó')
                    ->options([
                        'easy' => 'Dễ',
                        'medium' => 'Trung bình',
                        'hard' => 'Khó',
                        'expert' => 'Chuyên gia',
                    ]),
                Tables\Filters\Filter::make('created_today')
                    ->label('Tạo hôm nay')
                    ->query(fn(Builder $query): Builder => $query->whereDate('created_at', today())),
                Tables\Filters\Filter::make('pending_approval')
                    ->label('Chờ phê duyệt')
                    ->query(fn(Builder $query): Builder => $query->where('status', 'pending')),
                Tables\Filters\Filter::make('my_recipes')
                    ->label('Công thức của tôi')
                    ->query(fn(Builder $query): Builder => $query->where('user_id', Auth::id())),
                Tables\Filters\Filter::make('others_recipes')
                    ->label('Công thức người khác')
                    ->query(fn(Builder $query): Builder => $query->where('user_id', '!=', Auth::id())),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Xem chi tiết'),

                    // Chỉ cho phép chỉnh sửa công thức của chính mình
                    Tables\Actions\EditAction::make()
                        ->label('Chỉnh sửa')
                        ->visible(fn(Recipe $record): bool => Auth::user()->id === $record->user_id),

                    // Chỉ cho phép xóa công thức của chính mình
                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa')
                        ->visible(fn(Recipe $record): bool => Auth::user()->id === $record->user_id),

                    // ACTION DUYỆT: Chỉ với công thức của người khác
                    Tables\Actions\Action::make('approve')
                        ->label('Phê duyệt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Phê duyệt công thức')
                        ->modalDescription('Bạn có chắc chắn muốn phê duyệt công thức này?')
                        ->modalSubmitActionLabel('Phê duyệt')
                        ->action(function (Recipe $record) {
                            app(RecipeService::class)->approve($record, Auth::user());
                        })
                        ->visible(
                            fn(Recipe $record): bool =>
                            $record->status === 'pending' && Auth::user()->id !== $record->user_id
                        ),

                    // ACTION TỪ CHỐI: Chỉ với công thức của người khác
                    Tables\Actions\Action::make('reject')
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
                        ->action(function (Recipe $record, array $data) {
                            app(RecipeService::class)->reject($record, Auth::user(), $data['reason']);
                        })
                        ->visible(
                            fn(Recipe $record): bool =>
                            $record->status === 'pending' && Auth::user()->id !== $record->user_id
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // BULK ACTION CHÍNH: Phê duyệt hàng loạt
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Phê duyệt đã chọn')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Phê duyệt công thức đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn phê duyệt tất cả công thức đã chọn?')
                        ->modalSubmitActionLabel('Phê duyệt tất cả')
                        ->action(function (Collection $records) {
                            $service = app(RecipeService::class);
                            $user = Auth::user();
                            $count = 0;
                            foreach ($records as $record) {
                                // Chỉ duyệt công thức của người khác
                                if ($record->status === 'pending' && $record->user_id !== $user->id) {
                                    $service->approve($record, $user);
                                    $count++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title("Đã phê duyệt {$count} công thức")
                                ->success()
                                ->send();
                        }),

                    // BULK ACTION CHÍNH: Từ chối hàng loạt
                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Từ chối đã chọn')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Lý do từ chối')
                                ->required()
                                ->maxLength(500)
                                ->placeholder('Nhập lý do từ chối chung cho tất cả công thức...'),
                        ])
                        ->modalHeading('Từ chối công thức đã chọn')
                        ->modalDescription('Lý do từ chối sẽ được áp dụng cho tất cả công thức đã chọn.')
                        ->action(function (Collection $records, array $data) {
                            $service = app(RecipeService::class);
                            $user = Auth::user();
                            $count = 0;
                            foreach ($records as $record) {
                                // Chỉ từ chối công thức của người khác
                                if ($record->status === 'pending' && $record->user_id !== $user->id) {
                                    $service->reject($record, $user, $data['reason']);
                                    $count++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title("Đã từ chối {$count} công thức")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Tự động làm mới mỗi 30 giây để cập nhật trạng thái
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'view' => Pages\ViewRecipe::route('/{record}'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'categories', 'tags']);
    }

    /**
     * Kiểm tra Manager có thể chỉnh sửa công thức không
     * Manager chỉ có thể chỉnh sửa công thức của chính mình
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }

    /**
     * Kiểm tra Manager có thể xóa công thức không
     * Manager chỉ có thể xóa công thức của chính mình
     */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }
}
