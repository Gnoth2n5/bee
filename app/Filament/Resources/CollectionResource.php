<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Filament\Resources\CollectionResource\RelationManagers;
use App\Models\Collection;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Collection as CollectionSupport;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?string $navigationLabel = 'Bộ sưu tập';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin bộ sưu tập')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Chủ sở hữu')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Tên bộ sưu tập')
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
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Ảnh bìa')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_public')
                            ->label('Công khai')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Nổi bật')
                            ->default(false),
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
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Ảnh bìa')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên bộ sưu tập')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Chủ sở hữu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipes_count')
                    ->label('Số công thức')
                    ->counts('recipes')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Công khai')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Trạng thái công khai')
                    ->placeholder('Tất cả')
                    ->trueLabel('Công khai')
                    ->falseLabel('Riêng tư'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Trạng thái nổi bật')
                    ->placeholder('Tất cả')
                    ->trueLabel('Nổi bật')
                    ->falseLabel('Không nổi bật'),
                Tables\Filters\SelectFilter::make('user')
                    ->label('Chủ sở hữu')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_public')
                        ->label('Chuyển trạng thái công khai')
                        ->icon('heroicon-o-globe-alt')
                        ->action(fn(Collection $record) => $record->update(['is_public' => !$record->is_public]))
                        ->color(fn(Collection $record) => $record->is_public ? 'warning' : 'success'),
                    Tables\Actions\Action::make('toggle_featured')
                        ->label('Chuyển trạng thái nổi bật')
                        ->icon('heroicon-o-star')
                        ->action(fn(Collection $record) => $record->update(['is_featured' => !$record->is_featured]))
                        ->color(fn(Collection $record) => $record->is_featured ? 'warning' : 'success'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('make_public')
                        ->label('Đặt công khai')
                        ->icon('heroicon-o-globe-alt')
                        ->color('success')
                        ->action(fn(CollectionSupport $records) => $records->each->update(['is_public' => true])),
                    Tables\Actions\BulkAction::make('make_private')
                        ->label('Đặt riêng tư')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->action(fn(CollectionSupport $records) => $records->each->update(['is_public' => false])),
                    Tables\Actions\BulkAction::make('make_featured')
                        ->label('Đặt nổi bật')
                        ->icon('heroicon-o-star')
                        ->color('success')
                        ->action(fn(CollectionSupport $records) => $records->each->update(['is_featured' => true])),
                    Tables\Actions\BulkAction::make('remove_featured')
                        ->label('Bỏ nổi bật')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(fn(CollectionSupport $records) => $records->each->update(['is_featured' => false])),
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'recipes']);
    }
}