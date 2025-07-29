<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PopularCategoriesWidget extends BaseWidget
{
    protected static ?string $heading = 'Danh mục phổ biến';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Category::query()
                    ->withCount('recipes')
                    ->where('is_active', true)
                    ->orderBy('recipes_count', 'desc')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('recipes_count')
                    ->label('Số công thức')
                    ->counts('recipes')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Danh mục cha')
                    ->placeholder('Danh mục chính')
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->icon('heroicon-o-eye')
                    ->url(
                        fn(Category $record): string =>
                        route('filament.admin.resources.categories.edit', $record)
                    ),
            ])
            ->paginated(false);
    }
}