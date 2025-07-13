<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Filament\Resources\RatingResource\RelationManagers;
use App\Models\Rating;
use App\Models\User;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationGroup = 'Quản lý nội dung';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin đánh giá')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Người đánh giá')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('recipe_id')
                            ->label('Công thức')
                            ->options(Recipe::pluck('title', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('rating')
                            ->label('Điểm đánh giá')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->required()
                            ->step(0.5),
                        Forms\Components\Textarea::make('comment')
                            ->label('Nhận xét')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Đã phê duyệt')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Người đánh giá')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipe.title')
                    ->label('Công thức')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Điểm')
                    ->numeric(
                        decimalPlaces: 1,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->sortable()
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 4.5 => 'success',
                        $state >= 3.5 => 'warning',
                        $state >= 2.5 => 'info',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Nhận xét')
                    ->limit(100)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Đã phê duyệt')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Trạng thái phê duyệt')
                    ->placeholder('Tất cả')
                    ->trueLabel('Đã phê duyệt')
                    ->falseLabel('Chưa phê duyệt'),
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Điểm đánh giá')
                    ->options([
                        '5' => '5 sao',
                        '4' => '4 sao',
                        '3' => '3 sao',
                        '2' => '2 sao',
                        '1' => '1 sao',
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->label('Người đánh giá')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('recipe')
                    ->label('Công thức')
                    ->relationship('recipe', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('approve')
                        ->label('Phê duyệt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Rating $record) => $record->update(['is_approved' => true]))
                        ->visible(fn (Rating $record) => !$record->is_approved),
                    Tables\Actions\Action::make('reject')
                        ->label('Từ chối')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Rating $record) => $record->update(['is_approved' => false]))
                        ->visible(fn (Rating $record) => $record->is_approved),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Phê duyệt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => true])),
                    Tables\Actions\BulkAction::make('reject')
                        ->label('Từ chối')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['is_approved' => false])),
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
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'recipe']);
    }
}
