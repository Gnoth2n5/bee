<?php

namespace App\Filament\UserResources;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserRecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;
    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationLabel = 'Công thức của tôi';
    protected static ?string $navigationGroup = 'Quản lý cá nhân';
    protected static ?int $navigationSort = 1;

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
                            ->live(onBlur: true),
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
                        Forms\Components\Select::make('difficulty')
                            ->label('Độ khó')
                            ->options([
                                'easy' => 'Dễ',
                                'medium' => 'Trung bình',
                                'hard' => 'Khó',
                            ])
                            ->default('medium')
                            ->required(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Danh mục')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('difficulty')
                    ->label('Độ khó')
                    ->badge(),
                Tables\Columns\TextColumn::make('servings')
                    ->label('Khẩu phần')
                    ->numeric(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                // Có thể thêm filter nếu cần
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        // Chỉ lấy công thức của user hiện tại
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => UserRecipeResource\Pages\ListUserRecipes::route('/'),
            'create' => UserRecipeResource\Pages\CreateUserRecipe::route('/create'),
            'edit' => UserRecipeResource\Pages\EditUserRecipe::route('/{record}/edit'),
        ];
    }
}