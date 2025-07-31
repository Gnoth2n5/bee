<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VietnamCityResource\Pages;
use App\Models\VietnamCity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VietnamCityResource extends Resource
{
    protected static ?string $model = VietnamCity::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Quản lý thời tiết';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Thành phố Việt Nam';

    protected static ?string $pluralModelLabel = 'Thành phố Việt Nam';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin thành phố')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên thành phố')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Mã thành phố')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('region')
                            ->label('Vùng miền')
                            ->options([
                                'Bắc' => 'Miền Bắc',
                                'Trung' => 'Miền Trung',
                                'Nam' => 'Miền Nam',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Vĩ độ')
                            ->numeric()
                            ->required()
                            ->step(0.00000001),
                        Forms\Components\TextInput::make('longitude')
                            ->label('Kinh độ')
                            ->numeric()
                            ->required()
                            ->step(0.00000001),
                        Forms\Components\TextInput::make('timezone')
                            ->label('Múi giờ')
                            ->default('Asia/Ho_Chi_Minh')
                            ->maxLength(50),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kích hoạt')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Thứ tự sắp xếp')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên thành phố')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('region')
                    ->label('Vùng miền')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Bắc' => 'primary',
                        'Trung' => 'warning',
                        'Nam' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Vĩ độ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Kinh độ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Trạng thái')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latestWeatherData.temperature')
                    ->label('Nhiệt độ hiện tại')
                    ->suffix('°C')
                    ->sortable(),
                Tables\Columns\TextColumn::make('latestWeatherData.weather_description')
                    ->label('Thời tiết')
                    ->limit(20),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region')
                    ->label('Vùng miền')
                    ->options([
                        'Bắc' => 'Miền Bắc',
                        'Trung' => 'Miền Trung',
                        'Nam' => 'Miền Nam',
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
                    Tables\Actions\Action::make('update_weather')
                        ->label('Cập nhật thời tiết')
                        ->icon('heroicon-o-cloud')
                        ->color('success')
                        ->action(function (VietnamCity $record) {
                            $weatherService = new \App\Services\WeatherService();
                            $weatherService->getCurrentWeather($record);
                            \Filament\Notifications\Notification::make()
                                ->title('Đã cập nhật thời tiết')
                                ->success()
                                ->send();
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
                    Tables\Actions\BulkAction::make('update_weather_bulk')
                        ->label('Cập nhật thời tiết')
                        ->icon('heroicon-o-cloud')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $weatherService = new \App\Services\WeatherService();
                            $count = 0;

                            foreach ($records as $record) {
                                $weatherService->getCurrentWeather($record);
                                $count++;
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("Đã cập nhật thời tiết cho $count thành phố")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListVietnamCities::route('/'),
            'create' => Pages\CreateVietnamCity::route('/create'),
            'edit' => Pages\EditVietnamCity::route('/{record}/edit'),
        ];
    }
}