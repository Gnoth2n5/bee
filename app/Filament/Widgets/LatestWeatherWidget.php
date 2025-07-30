<?php

namespace App\Filament\Widgets;

use App\Models\WeatherData;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestWeatherWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WeatherData::query()
                    ->where('last_updated', '>=', now()->subHours(6))
                    ->orderBy('last_updated', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('city_name')
                    ->label('Thành phố')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature')
                    ->label('Nhiệt độ')
                    ->suffix('°C')
                    ->sortable()
                    ->color(fn(string $state): string => match (true) {
                        $state < 15 => 'info',
                        $state > 30 => 'danger',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('humidity')
                    ->label('Độ ẩm')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weather_description')
                    ->label('Thời tiết')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('wind_speed')
                    ->label('Gió')
                    ->suffix(' m/s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_updated')
                    ->label('Cập nhật lúc')
                    ->dateTime('H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('weather_condition')
                    ->label('Điều kiện thời tiết')
                    ->options([
                        'Clear' => 'Trời quang',
                        'Clouds' => 'Có mây',
                        'Rain' => 'Mưa',
                        'Snow' => 'Tuyết',
                        'Thunderstorm' => 'Giông bão',
                        'Drizzle' => 'Mưa phùn',
                        'Mist' => 'Sương mù',
                        'Fog' => 'Sương mù',
                        'Haze' => 'Sương mù nhẹ',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('Xem chi tiết')
                    ->icon('heroicon-o-eye')
                    ->url(fn(WeatherData $record): string => route('filament.admin.resources.weather-data.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}