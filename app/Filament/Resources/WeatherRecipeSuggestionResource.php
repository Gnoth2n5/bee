<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeatherRecipeSuggestionResource\Pages;
use App\Models\WeatherRecipeSuggestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class WeatherRecipeSuggestionResource extends Resource
{
    protected static ?string $model = WeatherRecipeSuggestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?string $navigationGroup = 'Quản lý thời tiết';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Đề xuất món ăn theo thời tiết';

    protected static ?string $pluralModelLabel = 'Đề xuất món ăn theo thời tiết';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Ẩn khỏi navigation admin
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin đề xuất')
                    ->schema([
                        Forms\Components\TextInput::make('city_code')
                            ->label('Mã thành phố')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('weather_condition')
                            ->label('Điều kiện thời tiết')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('temperature_min')
                            ->label('Nhiệt độ tối thiểu (°C)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('temperature_max')
                            ->label('Nhiệt độ tối đa (°C)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('humidity_min')
                            ->label('Độ ẩm tối thiểu (%)')
                            ->numeric(),
                        Forms\Components\TextInput::make('humidity_max')
                            ->label('Độ ẩm tối đa (%)')
                            ->numeric(),
                        Forms\Components\Textarea::make('suggestion_reason')
                            ->label('Lý do đề xuất')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kích hoạt')
                            ->default(true),
                        Forms\Components\TextInput::make('priority')
                            ->label('Độ ưu tiên')
                            ->numeric()
                            ->default(1),
                        Forms\Components\DateTimePicker::make('last_generated')
                            ->label('Tạo lúc')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Thành phố')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weather_condition')
                    ->label('Điều kiện thời tiết')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature_min')
                    ->label('Nhiệt độ min')
                    ->suffix('°C')
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature_max')
                    ->label('Nhiệt độ max')
                    ->suffix('°C')
                    ->sortable(),
                Tables\Columns\TextColumn::make('humidity_min')
                    ->label('Độ ẩm min')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('humidity_max')
                    ->label('Độ ẩm max')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('suggestion_reason')
                    ->label('Lý do đề xuất')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Ưu tiên')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_generated')
                    ->label('Tạo lúc')
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->placeholder('Tất cả')
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Không hoạt động'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('regenerate_suggestions')
                        ->label('Tạo lại đề xuất')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->action(function (WeatherRecipeSuggestion $record) {
                            $weatherRecipeService = new \App\Services\WeatherRecipeService(new \App\Services\WeatherService());
                            $city = \App\Models\VietnamCity::where('code', $record->city_code)->first();
                            if ($city) {
                                $weatherData = $weatherRecipeService->getWeatherService()->getCachedWeather($city);
                                if ($weatherData) {
                                    $weatherRecipeService->saveWeatherSuggestions($city, $weatherData);
                                    \Filament\Notifications\Notification::make()
                                        ->title('Đã tạo lại đề xuất')
                                        ->success()
                                        ->send();
                                }
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Kích hoạt')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(Collection $records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Tắt kích hoạt')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn(Collection $records) => $records->each->update(['is_active' => false])),
                    Tables\Actions\BulkAction::make('regenerate_all_suggestions')
                        ->label('Tạo lại tất cả đề xuất')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $weatherRecipeService = new \App\Services\WeatherRecipeService(new \App\Services\WeatherService());
                            $count = 0;

                            foreach ($records as $record) {
                                $city = \App\Models\VietnamCity::where('code', $record->city_code)->first();
                                if ($city) {
                                    $weatherData = $weatherRecipeService->getWeatherService()->getCachedWeather($city);
                                    if ($weatherData) {
                                        $weatherRecipeService->saveWeatherSuggestions($city, $weatherData);
                                        $count++;
                                    }
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("Đã tạo lại đề xuất cho $count thành phố")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_generated', 'desc');
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
            'index' => Pages\ListWeatherRecipeSuggestions::route('/'),
            'create' => Pages\CreateWeatherRecipeSuggestion::route('/create'),
            'edit' => Pages\EditWeatherRecipeSuggestion::route('/{record}/edit'),
        ];
    }
}