<?php

namespace App\Filament\Widgets;

use App\Models\Recipe;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestRecipes extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Recipe::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Ảnh')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Tác giả')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->url(fn (Recipe $record): string => route('filament.admin.resources.recipes.edit', $record))
                    ->icon('heroicon-m-eye')
                    ->color('primary'),
            ])
            ->paginated(false);
    }
} 