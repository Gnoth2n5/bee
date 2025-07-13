<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';
    
    protected static ?string $navigationGroup = 'Quản lý nội dung';
    
    protected static ?int $navigationSort = 2;

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
                            ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
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
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
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
                            ->columnSpanFull()
                            ->itemLabel(fn (array $state): ?string => 'Bước: ' . Str::limit($state['instruction'] ?? '', 50)),
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
                    ->schema([
                        Forms\Components\Select::make('category_ids')
                            ->label('Danh mục')
                            ->multiple()
                            ->options(Category::pluck('name', 'id'))
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('tag_ids')
                            ->label('Thẻ')
                            ->multiple()
                            ->options(Tag::pluck('name', 'id'))
                            ->preload()
                            ->searchable(),
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
                            ->default('draft')
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
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Thời gian xuất bản'),
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
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Danh mục')
                    ->badge()
                    ->separator(',')
                    ->limit(2),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'published' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('difficulty')
                    ->label('Độ khó')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'easy' => 'success',
                        'medium' => 'warning',
                        'hard' => 'danger',
                        'expert' => 'purple',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('servings')
                    ->label('Khẩu phần')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cooking_time')
                    ->label('Thời gian nấu')
                    ->formatStateUsing(fn (int $state): string => $state . ' phút')
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Lượt xem')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Đánh giá TB')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Cập nhật lúc')
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
                    ]),
                Tables\Filters\SelectFilter::make('difficulty')
                    ->label('Độ khó')
                    ->options([
                        'easy' => 'Dễ',
                        'medium' => 'Trung bình',
                        'hard' => 'Khó',
                        'expert' => 'Chuyên gia',
                    ]),
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Danh mục')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\Filter::make('created_at')
                    ->label('Ngày tạo')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Từ ngày'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->icon('heroicon-o-pencil'),
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
                    ->visible(fn (Recipe $record): bool => $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Lý do từ chối')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('Nhập lý do từ chối công thức...'),
                    ])
                    ->modalHeading('Từ chối công thức')
                    ->modalDescription('Vui lòng cung cấp lý do từ chối công thức này.')
                    ->modalSubmitActionLabel('Từ chối')
                    ->action(function (Recipe $record, array $data) {
                        app(RecipeService::class)->reject($record, Auth::user(), $data['reason']);
                    })
                    ->visible(fn (Recipe $record): bool => $record->status === 'pending'),
                Tables\Actions\Action::make('publish')
                    ->label('Xuất bản')
                    ->icon('heroicon-o-globe-alt')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Xuất bản công thức')
                    ->modalDescription('Bạn có chắc chắn muốn xuất bản công thức này?')
                    ->modalSubmitActionLabel('Xuất bản')
                    ->action(function (Recipe $record) {
                        $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]);
                    })
                    ->visible(fn (Recipe $record): bool => $record->status === 'approved'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn')
                        ->icon('heroicon-o-trash'),
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Phê duyệt đã chọn')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Phê duyệt công thức đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn phê duyệt tất cả công thức đã chọn?')
                        ->modalSubmitActionLabel('Phê duyệt')
                        ->action(function (Collection $records) {
                            $service = app(RecipeService::class);
                            $user = Auth::user();
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $service->approve($record, $user);
                                    $count++;
                                }
                            }
                            return $count . ' công thức đã được phê duyệt.';
                        }),
                    Tables\Actions\BulkAction::make('reject_selected')
                        ->label('Từ chối đã chọn')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Lý do từ chối')
                                ->required()
                                ->maxLength(500)
                                ->placeholder('Nhập lý do từ chối các công thức...'),
                        ])
                        ->modalHeading('Từ chối công thức đã chọn')
                        ->modalDescription('Vui lòng cung cấp lý do từ chối các công thức đã chọn.')
                        ->modalSubmitActionLabel('Từ chối')
                        ->action(function (Collection $records, array $data) {
                            $service = app(RecipeService::class);
                            $user = Auth::user();
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $service->reject($record, $user, $data['reason']);
                                    $count++;
                                }
                            }
                            return $count . ' công thức đã được từ chối.';
                        }),
                    Tables\Actions\BulkAction::make('publish_selected')
                        ->label('Xuất bản đã chọn')
                        ->icon('heroicon-o-globe-alt')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Xuất bản công thức đã chọn')
                        ->modalDescription('Bạn có chắc chắn muốn xuất bản tất cả công thức đã chọn?')
                        ->modalSubmitActionLabel('Xuất bản')
                        ->action(function (Collection $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'approved') {
                                    $record->update([
                                        'status' => 'published',
                                        'published_at' => now(),
                                    ]);
                                    $count++;
                                }
                            }
                            return $count . ' công thức đã được xuất bản.';
                        }),
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
}
