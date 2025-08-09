<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeatherConditionRuleResource\Pages;
use App\Models\WeatherConditionRule;
use App\Models\Category;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class WeatherConditionRuleResource extends Resource
{
    protected static ?string $model = WeatherConditionRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationGroup = 'Quản lý thời tiết';

    protected static ?string $navigationLabel = 'Quy Tắc Thời Tiết';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return true; // Hiển thị trong navigation admin
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông Tin Cơ Bản')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên Quy Tắc')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('VD: Nhiệt độ cao độ ẩm cao'),

                        Textarea::make('description')
                            ->label('Mô Tả')
                            ->rows(3)
                            ->placeholder('Mô tả chi tiết về quy tắc này'),
                    ])->columns(2),

                Section::make('Điều Kiện Nhiệt Độ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('temperature_min')
                                    ->label('Nhiệt Độ Tối Thiểu (°C)')
                                    ->numeric()
                                    ->placeholder('VD: 24')
                                    ->helperText('Để trống nếu không có giới hạn dưới'),

                                TextInput::make('temperature_max')
                                    ->label('Nhiệt Độ Tối Đa (°C)')
                                    ->numeric()
                                    ->placeholder('VD: 30')
                                    ->helperText('Để trống nếu không có giới hạn trên'),
                            ]),
                    ]),

                Section::make('Điều Kiện Độ Ẩm')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('humidity_min')
                                    ->label('Độ Ẩm Tối Thiểu (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->placeholder('VD: 70')
                                    ->helperText('Để trống nếu không có giới hạn dưới'),

                                TextInput::make('humidity_max')
                                    ->label('Độ Ẩm Tối Đa (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->placeholder('VD: 90')
                                    ->helperText('Để trống nếu không có giới hạn trên'),
                            ]),
                    ]),

                Section::make('Đề Xuất Món Ăn')
                    ->schema([
                        Select::make('suggested_categories')
                            ->label('Danh Mục Đề Xuất')
                            ->multiple()
                            ->options(Category::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Chọn các danh mục món ăn phù hợp với điều kiện thời tiết này'),

                        Select::make('suggested_tags')
                            ->label('Tags Đề Xuất')
                            ->multiple()
                            ->options(Tag::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Chọn các tag phù hợp với điều kiện thời tiết này'),

                        Textarea::make('suggestion_reason')
                            ->label('Lý Do Đề Xuất')
                            ->required()
                            ->rows(3)
                            ->placeholder('Lý do đề xuất các món ăn này cho điều kiện thời tiết...'),
                    ]),

                Section::make('Cài Đặt')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Kích Hoạt')
                                    ->default(true)
                                    ->helperText('Bật/tắt quy tắc này'),

                                TextInput::make('priority')
                                    ->label('Độ Ưu Tiên')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->helperText('Độ ưu tiên (1-10, cao hơn = ưu tiên hơn)'),
                            ]),

                        KeyValue::make('seasonal_rules')
                            ->label('Quy Tắc Theo Mùa')
                            ->keyLabel('Mùa')
                            ->valueLabel('Tháng Hoạt Động')
                            ->helperText('Quy tắc theo mùa (tùy chọn)')
                            ->addActionLabel('Thêm Mùa')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên Quy Tắc')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('temperature_range')
                    ->label('Khoảng Nhiệt Độ')
                    ->getStateUsing(function (WeatherConditionRule $record): string {
                        return $record->getTemperatureRangeDescription();
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('humidity_range')
                    ->label('Khoảng Độ Ẩm')
                    ->getStateUsing(function (WeatherConditionRule $record): string {
                        return $record->getHumidityRangeDescription();
                    })
                    ->badge()
                    ->color('warning'),

                TextColumn::make('suggested_categories_count')
                    ->label('Danh Mục')
                    ->getStateUsing(function (WeatherConditionRule $record): int {
                        return count($record->suggested_categories ?? []);
                    })
                    ->badge()
                    ->color('success'),

                TextColumn::make('suggested_tags_count')
                    ->label('Tags')
                    ->getStateUsing(function (WeatherConditionRule $record): int {
                        return count($record->suggested_tags ?? []);
                    })
                    ->badge()
                    ->color('primary'),

                BooleanColumn::make('is_active')
                    ->label('Kích Hoạt')
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Ưu Tiên')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '1', '2' => 'gray',
                        '3', '4' => 'warning',
                        '5', '6', '7', '8', '9', '10' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('updated_at')
                    ->label('Cập Nhật Lần Cuối')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Trạng Thái')
                    ->options([
                        '1' => 'Kích Hoạt',
                        '0' => 'Tắt',
                    ]),

                Filter::make('temperature_range')
                    ->label('Khoảng Nhiệt Độ')
                    ->form([
                        TextInput::make('min_temp')
                            ->label('Nhiệt Độ Tối Thiểu')
                            ->numeric(),
                        TextInput::make('max_temp')
                            ->label('Nhiệt Độ Tối Đa')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_temp'],
                                fn(Builder $query, $minTemp): Builder => $query->where('temperature_min', '>=', $minTemp),
                            )
                            ->when(
                                $data['max_temp'],
                                fn(Builder $query, $maxTemp): Builder => $query->where('temperature_max', '<=', $maxTemp),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('test_rule')
                    ->label('Test Quy Tắc')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (WeatherConditionRule $record) {
                        // Logic để test rule
                        $recipes = $record->getMatchingRecipes(5);
                        return redirect()->back()->with('success', "Tìm thấy {$recipes->count()} món ăn phù hợp với quy tắc này.");
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('priority', 'desc');
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
            'index' => Pages\ListWeatherConditionRules::route('/'),
            'create' => Pages\CreateWeatherConditionRule::route('/create'),
            'edit' => Pages\EditWeatherConditionRule::route('/{record}/edit'),
        ];
    }
}