<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
                        Forms\Components\TextInput::make('calories_per_serving')
                            ->label('Calo mỗi khẩu phần')
                            ->numeric()
                            ->minValue(0),
                    ])->columns(3),
                
                Forms\Components\Section::make('Nội dung công thức')
                    ->schema([
                        Forms\Components\RichEditor::make('ingredients')
                            ->label('Nguyên liệu')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('instructions')
                            ->label('Hướng dẫn')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('tips')
                            ->label('Mẹo nấu ăn')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Ghi chú')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Ảnh đại diện')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('video_url')
                            ->label('URL Video')
                            ->url()
                            ->maxLength(500),
                    ])->columns(2),
                
                Forms\Components\Section::make('Phân loại')
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label('Danh mục')
                            ->multiple()
                            ->relationship('categories', 'name')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('tags')
                            ->label('Thẻ')
                            ->multiple()
                            ->relationship('tags', 'name')
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
                    ->limit(50),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tác giả')
                    ->searchable()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Lượt xem')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_rating')
                    ->label('Đánh giá TB')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable(),
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('approve')
                        ->label('Phê duyệt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Recipe $record) => $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->user()->id,
                        ]))
                        ->visible(fn (Recipe $record) => $record->status === 'pending'),
                    Tables\Actions\Action::make('reject')
                        ->label('Từ chối')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Lý do từ chối')
                                ->required(),
                        ])
                        ->action(function (Recipe $record, array $data) {
                            $record->update([
                                'status' => 'rejected',
                                'rejection_reason' => $data['rejection_reason'],
                            ]);
                        })
                        ->visible(fn (Recipe $record) => $record->status === 'pending'),
                    Tables\Actions\Action::make('publish')
                        ->label('Xuất bản')
                        ->icon('heroicon-o-globe-alt')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(fn (Recipe $record) => $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]))
                        ->visible(fn (Recipe $record) => $record->status === 'approved'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Phê duyệt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'approved',
                                    'approved_at' => now(),
                                    'approved_by' => auth()->user()->id,
                                ]);
                            });
                        }),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Từ chối')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Lý do từ chối')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'status' => 'rejected',
                                    'rejection_reason' => $data['rejection_reason'],
                                ]);
                            });
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'categories', 'tags']);
    }
}
