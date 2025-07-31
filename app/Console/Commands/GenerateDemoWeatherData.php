<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WeatherData;
use App\Models\VietnamCity;
use Carbon\Carbon;

class GenerateDemoWeatherData extends Command
{
    protected $signature = 'weather:demo {--city= : Mã thành phố cụ thể} {--all : Tạo cho tất cả thành phố}';
    protected $description = 'Tạo dữ liệu thời tiết demo cho testing';

    public function handle()
    {
        $this->info('🌤️  Tạo dữ liệu thời tiết demo...');

        if ($this->option('city')) {
            $cityCode = strtoupper($this->option('city'));
            $city = VietnamCity::where('code', $cityCode)->first();

            if (!$city) {
                $this->error("❌ Không tìm thấy thành phố với mã: $cityCode");
                return 1;
            }

            $this->generateDemoData($city);
            $this->info("✅ Đã tạo dữ liệu demo cho {$city->name}");

        } elseif ($this->option('all')) {
            $cities = VietnamCity::active()->get();
            $bar = $this->output->createProgressBar($cities->count());

            foreach ($cities as $city) {
                $this->generateDemoData($city);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("✅ Đã tạo dữ liệu demo cho {$cities->count()} thành phố");

        } else {
            // Tạo demo cho 5 thành phố chính
            $mainCities = ['HANOI', 'HCM', 'DANANG', 'HAIPHONG', 'CANTHO'];
            $count = 0;

            foreach ($mainCities as $cityCode) {
                $city = VietnamCity::where('code', $cityCode)->first();
                if ($city) {
                    $this->generateDemoData($city);
                    $count++;
                }
            }

            $this->info("✅ Đã tạo dữ liệu demo cho $count thành phố chính");
        }

        return 0;
    }

    private function generateDemoData(VietnamCity $city)
    {
        // Tạo dữ liệu thời tiết ngẫu nhiên nhưng hợp lý
        $weatherConditions = [
            ['main' => 'Clear', 'description' => 'trời quang', 'icon' => '01d'],
            ['main' => 'Clouds', 'description' => 'có mây', 'icon' => '02d'],
            ['main' => 'Rain', 'description' => 'mưa nhẹ', 'icon' => '10d'],
            ['main' => 'Rain', 'description' => 'mưa vừa', 'icon' => '09d'],
            ['main' => 'Thunderstorm', 'description' => 'giông bão', 'icon' => '11d'],
        ];

        $weather = $weatherConditions[array_rand($weatherConditions)];

        // Nhiệt độ theo vùng miền
        $baseTemp = match ($city->region) {
            'Bắc' => rand(15, 30),
            'Trung' => rand(20, 35),
            'Nam' => rand(25, 35),
            default => rand(20, 30)
        };

        $weatherData = [
            'city_name' => $city->name,
            'city_code' => $city->code,
            'temperature' => $baseTemp,
            'feels_like' => $baseTemp + rand(-2, 2),
            'humidity' => rand(60, 90),
            'wind_speed' => rand(1, 8),
            'weather_condition' => $weather['main'],
            'weather_description' => $weather['description'],
            'weather_icon' => $weather['icon'],
            'pressure' => rand(1000, 1020),
            'visibility' => rand(8000, 12000),
            'uv_index' => rand(1, 10),
            'forecast_data' => null,
            'last_updated' => now()
        ];

        WeatherData::updateOrCreate(
            ['city_code' => $city->code, 'last_updated' => now()->startOfHour()],
            $weatherData
        );
    }
}