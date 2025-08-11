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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * Resource quản lý công thức dành cho Manager - VERSION ĐƠN GIẢN
 * 
 * Sử dụng default view của Filament thay vì custom infolist để tránh lỗi
 */
class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationLabel = 'Quản lý công thức';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        // Chỉ cho phép user có role Manager
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return Auth::check() && $user->hasRole('manager');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    // FORM ĐẦY ĐỦ GIỐNG ADMIN - ĐỂ VIEW CHI TIẾT ĐẦY ĐỦ THÔNG TIN
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
                        Forms\Components\TextInput::make('summary')
                            ->label('Tóm tắt')
                            ->maxLength(500)
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
                        Forms\Components\TextInput::make('total_time')
                            ->label('Tổng thời gian (phút)')
                            ->numeric()
                            ->minValue(0)
                            ->disabled()
                            ->dehydrated(false),
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
                        Forms\Components\TextInput::make('calories_per_serving')
                            ->label('Calo mỗi khẩu phần')
                            ->numeric()
                            ->minValue(0),
                    ])->columns(3),

                Forms\Components\Section::make('Nguyên liệu')
                    ->schema([
                        Forms\Components\Repeater::make('ingredients')
                            ->label('Danh sách nguyên liệu')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Tên nguyên liệu')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ví dụ: Thịt bò, Gạo, Rau cải...'),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Số lượng')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Ví dụ: 500, 2, 1kg...'),
                                Forms\Components\TextInput::make('unit')
                                    ->label('Đơn vị')
                                    ->maxLength(50)
                                    ->placeholder('g, kg, cái, quả, muỗng...'),
                            ])
                            ->defaultItems(1)
                            ->minItems(1)
                            ->maxItems(50)
                            ->reorderable(false)
                            ->columnSpanFull()
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),
                    ]),

                Forms\Components\Section::make('Hướng dẫn nấu ăn')
                    ->schema([
                        Forms\Components\Repeater::make('instructions')
                            ->label('Các bước thực hiện')
                            ->schema([
                                Forms\Components\Textarea::make('instruction')
                                    ->label('Hướng dẫn')
                                    ->required()
                                    ->maxLength(1000)
                                    ->rows(3)
                                    ->placeholder('Mô tả chi tiết bước thực hiện...'),
                            ])
                            ->defaultItems(1)
                            ->minItems(1)
                            ->maxItems(20)
                            ->reorderable(true)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Mẹo và ghi chú')
                    ->schema([
                        Forms\Components\Textarea::make('tips')
                            ->label('Mẹo nấu ăn')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->image()
                            ->imageEditor()
                            ->directory('recipes')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('video_url')
                            ->label('URL Video')
                            ->url()
                            ->maxLength(500),
                    ])->columns(2),

                Forms\Components\Section::make('Phân loại')
                    ->description('Chọn danh mục và thẻ để phân loại công thức')
                    ->schema([
                        Forms\Components\Select::make('category_ids')
                            ->label('Danh mục')
                            ->multiple()
                            ->options(function () {
                                return Category::where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText('Chọn một hoặc nhiều danh mục cho công thức')
                            ->placeholder('Chọn danh mục...'),
                        Forms\Components\Select::make('tag_ids')
                            ->label('Thẻ')
                            ->multiple()
                            ->options(function () {
                                return Tag::orderBy('usage_count', 'desc')
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->preload()
                            ->searchable()
                            ->helperText('Chọn các thẻ phù hợp với công thức')
                            ->placeholder('Chọn thẻ...'),
                    ])->columns(2),

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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ phê duyệt',
                        'approved' => 'Đã phê duyệt',
                        'rejected' => 'Từ chối',
                    ])
                    ->default('pending'),
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
                    // BULK ACTION: Phê duyệt hàng loạt
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

                    // BULK ACTION: Từ chối hàng loạt
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
            ->poll('30s');
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
     */
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }

    /**
     * Kiểm tra Manager có thể xóa công thức không
     */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->id === $record->user_id;
    }
}
