<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeatherDataResource\Pages;
use App\Models\WeatherData;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WeatherDataResource extends Resource
{
    protected static ?string $model = WeatherData::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationGroup = 'Quản lý thời tiết';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Dữ liệu thời tiết';

    protected static ?string $pluralModelLabel = 'Dữ liệu thời tiết';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin thời tiết')
                    ->schema([
                        Forms\Components\TextInput::make('city_name')
                            ->label('Tên thành phố')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city_code')
                            ->label('Mã thành phố')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('temperature')
                            ->label('Nhiệt độ (°C)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('feels_like')
                            ->label('Nhiệt độ cảm nhận (°C)')
                            ->numeric(),
                        Forms\Components\TextInput::make('humidity')
                            ->label('Độ ẩm (%)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('wind_speed')
                            ->label('Tốc độ gió (m/s)')
                            ->numeric(),
                        Forms\Components\TextInput::make('weather_condition')
                            ->label('Điều kiện thời tiết')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('weather_description')
                            ->label('Mô tả thời tiết')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('weather_icon')
                            ->label('Icon thời tiết')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('pressure')
                            ->label('Áp suất (hPa)')
                            ->numeric(),
                        Forms\Components\TextInput::make('visibility')
                            ->label('Tầm nhìn (m)')
                            ->numeric(),
                        Forms\Components\TextInput::make('uv_index')
                            ->label('Chỉ số UV')
                            ->numeric(),
                        Forms\Components\DateTimePicker::make('last_updated')
                            ->label('Cập nhật lúc')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city_name')
                    ->label('Thành phố')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature')
                    ->label('Nhiệt độ')
                    ->suffix('°C')
                    ->sortable(),
                Tables\Columns\TextColumn::make('humidity')
                    ->label('Độ ẩm')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weather_condition')
                    ->label('Điều kiện')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weather_description')
                    ->label('Mô tả')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('wind_speed')
                    ->label('Gió')
                    ->suffix(' m/s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_updated')
                    ->label('Cập nhật lúc')
                    ->dateTime('d/m/Y H:i')
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
                        'Smoke' => 'Khói',
                        'Dust' => 'Bụi',
                        'Sand' => 'Cát',
                        'Ash' => 'Tro',
                        'Squall' => 'Gió mạnh',
                        'Tornado' => 'Lốc xoáy',
                    ]),
                Tables\Filters\Filter::make('temperature_range')
                    ->label('Nhiệt độ')
                    ->form([
                        Forms\Components\TextInput::make('min_temp')
                            ->label('Tối thiểu (°C)')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_temp')
                            ->label('Tối đa (°C)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_temp'],
                                fn(Builder $query, $minTemp): Builder => $query->where('temperature', '>=', $minTemp),
                            )
                            ->when(
                                $data['max_temp'],
                                fn(Builder $query, $maxTemp): Builder => $query->where('temperature', '<=', $maxTemp),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('refresh_weather')
                        ->label('Làm mới thời tiết')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->action(function (WeatherData $record) {
                            $city = \App\Models\VietnamCity::where('code', $record->city_code)->first();
                            if ($city) {
                                $weatherService = new \App\Services\WeatherService();
                                $result = $weatherService->getCurrentWeather($city);
                                
                                if ($result !== null) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Đã cập nhật thời tiết thành công')
                                        ->success()
                                        ->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Không thể cập nhật thời tiết')
                                        ->body('Có lỗi xảy ra khi lấy dữ liệu thời tiết từ API')
                                        ->danger()
                                        ->send();
                                }
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Không tìm thấy thành phố')
                                    ->body('Không thể tìm thấy thông tin thành phố với mã: ' . $record->city_code)
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('refresh_all_weather')
                        ->label('Làm mới tất cả')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $weatherService = new \App\Services\WeatherService();
                            $successCount = 0;
                            $errorCount = 0;
                            $notFoundCount = 0;

                            foreach ($records as $record) {
                                $city = \App\Models\VietnamCity::where('code', $record->city_code)->first();
                                if ($city) {
                                    $result = $weatherService->getCurrentWeather($city);
                                    if ($result !== null) {
                                        $successCount++;
                                    } else {
                                        $errorCount++;
                                    }
                                } else {
                                    $notFoundCount++;
                                }
                            }

                            $message = "Kết quả cập nhật: ";
                            $messageParts = [];
                            
                            if ($successCount > 0) {
                                $messageParts[] = "$successCount thành công";
                            }
                            if ($errorCount > 0) {
                                $messageParts[] = "$errorCount lỗi";
                            }
                            if ($notFoundCount > 0) {
                                $messageParts[] = "$notFoundCount không tìm thấy thành phố";
                            }

                            $message .= implode(', ', $messageParts);

                            if ($errorCount > 0 || $notFoundCount > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Cập nhật thời tiết hoàn tất')
                                    ->body($message)
                                    ->warning()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Cập nhật thời tiết thành công')
                                    ->body($message)
                                    ->success()
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_updated', 'desc');
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
            'index' => Pages\ListWeatherData::route('/'),
            'create' => Pages\CreateWeatherData::route('/create'),
            'edit' => Pages\EditWeatherData::route('/{record}/edit'),
        ];
    }
}