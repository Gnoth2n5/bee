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
                            ->label('Quốc gia')
                            ->options([
                                'VN' => 'Việt Nam',
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
                    ->label('Quốc gia')
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region')
                    ->label('Quốc gia')
                    ->options([
                        'VN' => 'Việt Nam',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->placeholder('Tất cả')
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Không hoạt động'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('getApiDetail')
                        ->label('Cập nhật')
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
                        ->visible(fn(VietnamCity $record) => !empty($record->api_data)),
                ]),
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
                                    // Validate dữ liệu trước khi xử lý
                                    if (empty($province['code']) || empty($province['name'])) {
                                        Log::warning("Bỏ qua tỉnh thiếu dữ liệu: " . json_encode($province));
                                        $skipped++;
                                        continue;
                                    }

                                    // Đảm bảo code là string và không rỗng
                                    $code = (string) $province['code'];
                                    if (empty($code)) {
                                        Log::warning("Bỏ qua tỉnh có code rỗng: {$province['name']}");
                                        $skipped++;
                                        continue;
                                    }

                                    // Tìm kiếm theo code trước (chính xác hơn)
                                    $city = VietnamCity::where('code', $code)->first();

                                    // Nếu không tìm thấy theo code, tìm theo tên
                                    if (!$city) {
                                        $cleanName = str_replace(['Thành phố ', 'Tỉnh '], '', $province['name']);
                                        $city = VietnamCity::whereRaw('REPLACE(REPLACE(name, "Thành phố ", ""), "Tỉnh ", "") = ?', [$cleanName])
                                            ->orWhere('name', $province['name'])
                                            ->first();
                                    }

                                    $data = [
                                        'name' => $province['name'],
                                        'code' => $code,
                                        'codename' => $province['codename'] ?? null,
                                        'region' => static::determineRegion($province['name']),
                                        'is_active' => true,
                                        'api_data' => json_encode($province),
                                    ];

                                    // Chỉ cập nhật tọa độ nếu API có dữ liệu và không rỗng
                                    if (!empty($province['latitude']) && !empty($province['longitude'])) {
                                        $data['latitude'] = (float) $province['latitude'];
                                        $data['longitude'] = (float) $province['longitude'];
                                    }
                                    // Nếu không có tọa độ từ API, giữ nguyên tọa độ hiện tại (nếu có)
            
                                    if ($city) {
                                        $city->update($data);
                                        $updated++;
                                        Log::info("Cập nhật thành phố: {$province['name']} (code: {$code})");
                                    } else {
                                        VietnamCity::create($data);
                                        $created++;
                                        Log::info("Tạo mới thành phố: {$province['name']} (code: {$code})");
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
                Action::make('resetAndSync')
                    ->label('Reset và đồng bộ')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reset và đồng bộ dữ liệu')
                    ->modalDescription('Xóa tất cả dữ liệu thành phố hiện tại và đồng bộ lại từ API')
                    ->modalSubmitActionLabel('Reset và đồng bộ')
                    ->modalCancelActionLabel('Hủy')
                    ->action(function () {
                        try {
                            // Xóa tất cả dữ liệu cũ
                            $deletedCount = VietnamCity::count();
                            VietnamCity::truncate();

                            // Xóa cache
                            $provinceService = new VietnamProvinceService();
                            $provinceService->clearCache();

                            // Đồng bộ lại từ API
                            $provinces = $provinceService->getAllProvinces();

                            if (empty($provinces)) {
                                throw new \Exception('Không lấy được dữ liệu tỉnh thành từ API');
                            }

                            $created = 0;
                            $skipped = 0;

                            foreach ($provinces as $province) {
                                try {
                                    // Validate dữ liệu
                                    if (empty($province['code']) || empty($province['name'])) {
                                        $skipped++;
                                        continue;
                                    }

                                    $code = (string) $province['code'];
                                    if (empty($code)) {
                                        $skipped++;
                                        continue;
                                    }

                                    $data = [
                                        'name' => $province['name'],
                                        'code' => $code,
                                        'codename' => $province['codename'] ?? null,
                                        'region' => static::determineRegion($province['name']),
                                        'is_active' => true,
                                        'api_data' => json_encode($province),
                                    ];

                                    // Chỉ cập nhật tọa độ nếu API có dữ liệu và không rỗng
                                    if (!empty($province['latitude']) && !empty($province['longitude'])) {
                                        $data['latitude'] = (float) $province['latitude'];
                                        $data['longitude'] = (float) $province['longitude'];
                                    }

                                    VietnamCity::create($data);
                                    $created++;

                                } catch (\Exception $e) {
                                    Log::error("Lỗi khi tạo tỉnh {$province['name']}: " . $e->getMessage());
                                    $skipped++;
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Reset và đồng bộ thành công!')
                                ->body("Đã xóa {$deletedCount} bản ghi cũ, tạo mới: {$created}, bỏ qua: {$skipped}")
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Lỗi khi reset và đồng bộ: ' . $e->getMessage());

                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi reset và đồng bộ!')
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
                    }),
                Action::make('updateCoordinates')
                    ->label('Cập nhật tọa độ')
                    ->icon('heroicon-o-map-pin')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Cập nhật tọa độ')
                    ->modalDescription('Cập nhật tọa độ chính xác cho tất cả thành phố từ dữ liệu nội bộ')
                    ->modalSubmitActionLabel('Cập nhật')
                    ->modalCancelActionLabel('Hủy')
                    ->action(function () {
                        try {
                            $seeder = new \Database\Seeders\VietnamCityCoordinatesSeeder();
                            $seeder->run();

                            \Filament\Notifications\Notification::make()
                                ->title('Cập nhật tọa độ thành công!')
                                ->body('Đã cập nhật tọa độ chính xác cho các thành phố')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Lỗi khi cập nhật tọa độ: ' . $e->getMessage());

                            \Filament\Notifications\Notification::make()
                                ->title('Lỗi cập nhật tọa độ!')
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