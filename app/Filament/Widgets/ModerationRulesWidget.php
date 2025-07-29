<?php

namespace App\Filament\Widgets;

use App\Models\ModerationRule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ModerationRulesWidget extends BaseWidget
{
    protected static ?string $heading = 'Quy tắc kiểm duyệt gần đây';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ModerationRule::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên quy tắc')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('keywords')
                    ->label('Từ khóa')
                    ->limit(40)
                    ->tooltip(function (ModerationRule $record): string {
                        return $record->keywords;
                    }),

                Tables\Columns\BadgeColumn::make('action')
                    ->label('Hành động')
                    ->colors([
                        'danger' => 'reject',
                        'warning' => 'flag',
                        'success' => 'auto_approve',
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'reject' => 'Từ chối',
                            'flag' => 'Đánh dấu',
                            'auto_approve' => 'Tự động phê duyệt',
                            default => $state,
                        };
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Độ ưu tiên')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->icon('heroicon-o-eye')
                    ->url(
                        fn(ModerationRule $record): string =>
                        route('filament.admin.resources.moderation-rules.edit', $record)
                    ),
            ])
            ->paginated(false);
    }
}