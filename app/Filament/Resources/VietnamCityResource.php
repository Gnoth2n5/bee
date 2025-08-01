<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VietnamCityResource\Pages;
use App\Models\VietnamCity;
use App\Services\VietnamProvinceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class VietnamCityResource extends Resource
{
    protected static ?string $model = VietnamCity::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Quản lý thời tiết';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Thành phố';

    protected static ?string $pluralModelLabel = 'Thành phố';

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
                Tables\Columns\TextColumn::make('api_data')
                    ->label('Dữ liệu API')
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return 'Chưa có';
                        $data = json_decode($state, true);
                        return $data ? 'Có dữ liệu' : 'Lỗi dữ liệu';
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'Có dữ liệu' ? 'success' : 'gray'),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('getApiDetail')
                    ->label('Lấy từ API')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->size('sm')
                    ->requiresConfirmation()
                    ->modalHeading('Lấy thông tin từ API')
                    ->modalDescription('Cập nhật thông tin thành phố này từ OpenAPI Vietnam')
                    ->modalSubmitActionLabel('Cập nhật')
                    ->modalCancelActionLabel('Hủy')
                    ->action(function (VietnamCity $record) {
                        try {
                            $provinceService = new VietnamProvinceService();

                            // Lấy thông tin chi tiết từ API
                            $provinceData = $provinceService->getProvinceByCode($record->code);

                            if (!$provinceData) {
                                throw new \Exception('Không tìm thấy thông tin tỉnh thành trong API');
                            }

                            // Cập nhật dữ liệu
                            $record->update([
                                'name' => $provinceData['name'],
                                'codename' => $provinceData['codename'] ?? $record->codename,
                                'region' => static::determineRegion($provinceData['name']),
                                'latitude' => $provinceData['latitude'] ?? $record->latitude,
                                'longitude' => $provinceData['longitude'] ?? $record->longitude,
                                'api_data' => json_encode($provinceData),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Cập nhật thành công!')
                                ->body("Đã cập nhật thông tin thành phố: {$provinceData['name']}")
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error("Lỗi khi cập nhật thành phố {$record->name}: " . $e->getMessage());

                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi cập nhật!')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn(VietnamCity $record) => !empty($record->code)),
                Action::make('viewApiData')
                    ->label('Xem dữ liệu API')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->size('sm')
                    ->modalHeading('Dữ liệu API')
                    ->modalContent(function (VietnamCity $record) {
                        if (!$record->api_data) {
                            return view('components.empty-state', [
                                'title' => 'Chưa có dữ liệu API',
                                'description' => 'Thành phố này chưa được đồng bộ từ API'
                            ]);
                        }
                        
                        $data = json_decode($record->api_data, true);
                        if (!$data) {
                            return view('components.empty-state', [
                                'title' => 'Dữ liệu API lỗi',
                                'description' => 'Không thể đọc dữ liệu API'
                            ]);
                        }
                        
                        return view('components.api-data-view', [
                            'data' => $data,
                            'cityName' => $record->name
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->visible(fn (VietnamCity $record) => !empty($record->api_data)),
                Action::make('getDistricts')
                    ->label('Lấy quận/huyện')
                    ->icon('heroicon-o-map')
                    ->color('purple')
                    ->size('sm')
                    ->requiresConfirmation()
                    ->modalHeading('Lấy thông tin quận/huyện')
                    ->modalDescription('Lấy danh sách quận/huyện của thành phố này từ API')
                    ->modalSubmitActionLabel('Lấy dữ liệu')
                    ->modalCancelActionLabel('Hủy')
                    ->action(function (VietnamCity $record) {
                        try {
                            $provinceService = new VietnamProvinceService();
                            
                            // Lấy thông tin quận/huyện từ API
                            $districts = $provinceService->getDistrictsByProvinceCode($record->code);
                            
                            if (empty($districts)) {
                                throw new \Exception('Không tìm thấy thông tin quận/huyện trong API');
                            }
                            
                            // Cập nhật dữ liệu API với thông tin quận/huyện
                            $currentApiData = json_decode($record->api_data, true) ?: [];
                            $currentApiData['districts'] = $districts;
                            
                            $record->update([
                                'api_data' => json_encode($currentApiData),
                            ]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Lấy quận/huyện thành công!')
                                ->body("Đã lấy " . count($districts) . " quận/huyện cho thành phố: {$record->name}")
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Log::error("Lỗi khi lấy quận/huyện cho thành phố {$record->name}: " . $e->getMessage());
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi lấy quận/huyện!')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (VietnamCity $record) => !empty($record->code))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Action::make('refreshFromApi')
                        ->label('Refresh từ API')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Refresh dữ liệu từ API')
                        ->modalDescription('Cập nhật thông tin của các thành phố đã chọn từ OpenAPI Vietnam')
                        ->modalSubmitActionLabel('Cập nhật')
                        ->modalCancelActionLabel('Hủy')
                        ->action(function (Collection $records) {
                            $provinceService = new VietnamProvinceService();
                            $successCount = 0;
                            $errorCount = 0;
                            
                            foreach ($records as $record) {
                                try {
                                    if (empty($record->code)) {
                                        $errorCount++;
                                        continue;
                                    }
                                    
                                    // Lấy thông tin chi tiết từ API
                                    $provinceData = $provinceService->getProvinceByCode($record->code);
                                    
                                    if ($provinceData) {
                                        // Cập nhật dữ liệu
                                        $record->update([
                                            'name' => $provinceData['name'],
                                            'codename' => $provinceData['codename'] ?? $record->codename,
                                            'region' => static::determineRegion($provinceData['name']),
                                            'latitude' => $provinceData['latitude'] ?? $record->latitude,
                                            'longitude' => $provinceData['longitude'] ?? $record->longitude,
                                            'api_data' => json_encode($provinceData),
                                        ]);
                                        $successCount++;
                                    } else {
                                        $errorCount++;
                                    }
                                } catch (\Exception $e) {
                                    Log::error("Lỗi khi refresh thành phố {$record->name}: " . $e->getMessage());
                                    $errorCount++;
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Refresh hoàn tất!')
                                ->body("Thành công: {$successCount}, Lỗi: {$errorCount}")
                                ->success()
                                ->send();
                        })
                ]),
            ])
            ->headerActions([
                Action::make('getApi')
                    ->label('Lấy dữ liệu từ API')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Lấy dữ liệu từ API tỉnh thành')
                    ->modalDescription('Hành động này sẽ đồng bộ dữ liệu tỉnh thành từ OpenAPI Vietnam. Dữ liệu hiện tại có thể bị ghi đè.')
                    ->modalSubmitActionLabel('Đồng bộ ngay')
                    ->modalCancelActionLabel('Hủy')
                    ->action(function () {
                        try {
                            $provinceService = new VietnamProvinceService();

                            // Kiểm tra kết nối API
                            if (!$provinceService->testConnection()) {
                                throw new \Exception('Không thể kết nối đến API OpenAPI Vietnam');
                            }

                            // Lấy dữ liệu từ API
                            $provinces = $provinceService->getAllProvinces();

                            if (empty($provinces)) {
                                throw new \Exception('Không lấy được dữ liệu tỉnh thành từ API');
                            }

                            $created = 0;
                            $updated = 0;
                            $skipped = 0;

                            foreach ($provinces as $province) {
                                try {
                                    // Tìm kiếm theo tên (loại bỏ "Thành phố", "Tỉnh" để so sánh)
                                    $cleanName = str_replace(['Thành phố ', 'Tỉnh '], '', $province['name']);
                                    $city = VietnamCity::whereRaw('REPLACE(REPLACE(name, "Thành phố ", ""), "Tỉnh ", "") = ?', [$cleanName])
                                        ->orWhere('name', $province['name'])
                                        ->first();

                                    $data = [
                                        'name' => $province['name'],
                                        'code' => (string) $province['code'],
                                        'codename' => $province['codename'] ?? null,
                                        'region' => static::determineRegion($province['name']),
                                        'latitude' => $province['latitude'] ?? null,
                                        'longitude' => $province['longitude'] ?? null,
                                        'is_active' => true,
                                        'api_data' => json_encode($province),
                                    ];

                                    if ($city) {
                                        $city->update($data);
                                        $updated++;
                                    } else {
                                        VietnamCity::create($data);
                                        $created++;
                                    }
                                } catch (\Exception $e) {
                                    Log::error("Lỗi khi đồng bộ tỉnh {$province['name']}: " . $e->getMessage());
                                    $skipped++;
                                }
                            }

                            // Hiển thị thông báo thành công
                            \Filament\Notifications\Notification::make()
                                ->title('Đồng bộ thành công!')
                                ->body("Đã tạo mới: {$created}, Cập nhật: {$updated}, Bỏ qua: {$skipped}")
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Lỗi khi đồng bộ dữ liệu tỉnh thành: ' . $e->getMessage());

                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi đồng bộ!')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('clearCache')
                    ->label('Xóa cache API')
                    ->icon('heroicon-o-trash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Xóa cache API')
                    ->modalDescription('Xóa cache dữ liệu API tỉnh thành để lấy dữ liệu mới nhất')
                    ->modalSubmitActionLabel('Xóa cache')
                    ->modalCancelActionLabel('Hủy')
                    ->action(function () {
                        try {
                            $provinceService = new VietnamProvinceService();
                            $provinceService->clearCache();

                            \Filament\Notifications\Notification::make()
                                ->title('Xóa cache thành công!')
                                ->body('Đã xóa cache dữ liệu API tỉnh thành')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Lỗi khi xóa cache API: ' . $e->getMessage());

                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi xóa cache!')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('testApi')
                    ->label('Kiểm tra API')
                    ->icon('heroicon-o-signal')
                    ->color('info')
                    ->action(function () {
                        try {
                            $provinceService = new VietnamProvinceService();

                            if ($provinceService->testConnection()) {
                                $stats = $provinceService->getStats();

                                \Filament\Notifications\Notification::make()
                                    ->title('API hoạt động bình thường!')
                                    ->body("Tổng số tỉnh: {$stats['total_provinces']}, Trạng thái: {$stats['api_status']}")
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('API không khả dụng!')
                                    ->body('Không thể kết nối đến OpenAPI Vietnam')
                                    ->danger()
                                    ->send();
                            }

                        } catch (\Exception $e) {
                            Log::error('Lỗi khi kiểm tra API: ' . $e->getMessage());

                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi kiểm tra API!')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
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

    /**
     * Xác định vùng miền dựa trên tên tỉnh
     */
    protected static function determineRegion($provinceName)
    {
        $northProvinces = [
            'Hà Nội',
            'Hải Phòng',
            'Quảng Ninh',
            'Bắc Giang',
            'Bắc Ninh',
            'Hải Dương',
            'Hưng Yên',
            'Hòa Bình',
            'Phú Thọ',
            'Thái Nguyên',
            'Tuyên Quang',
            'Lào Cai',
            'Yên Bái',
            'Lạng Sơn',
            'Cao Bằng',
            'Bắc Kạn',
            'Thái Bình',
            'Nam Định',
            'Ninh Bình',
            'Thanh Hóa',
            'Nghệ An',
            'Hà Tĩnh',
            'Quảng Bình',
            'Quảng Trị',
            'Thừa Thiên Huế',
            'Điện Biên',
            'Lai Châu',
            'Sơn La',
            'Hà Giang'
        ];

        $centralProvinces = [
            'Đà Nẵng',
            'Quảng Nam',
            'Quảng Ngãi',
            'Bình Định',
            'Phú Yên',
            'Khánh Hòa',
            'Ninh Thuận',
            'Bình Thuận',
            'Kon Tum',
            'Gia Lai',
            'Đắk Lắk',
            'Đắk Nông',
            'Lâm Đồng',
            'Bình Phước',
            'Tây Ninh',
            'Bình Dương',
            'Đồng Nai',
            'Bà Rịa - Vũng Tàu'
        ];

        $southProvinces = [
            'TP. Hồ Chí Minh',
            'Long An',
            'Tiền Giang',
            'Bến Tre',
            'Trà Vinh',
            'Vĩnh Long',
            'Đồng Tháp',
            'An Giang',
            'Kiên Giang',
            'Cần Thơ',
            'Hậu Giang',
            'Sóc Trăng',
            'Bạc Liêu',
            'Cà Mau'
        ];

        if (in_array($provinceName, $northProvinces)) {
            return 'Bắc';
        } elseif (in_array($provinceName, $centralProvinces)) {
            return 'Trung';
        } elseif (in_array($provinceName, $southProvinces)) {
            return 'Nam';
        }

        return 'Khác';
    }
}