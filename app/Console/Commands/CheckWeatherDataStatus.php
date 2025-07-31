<?php

namespace App\Console\Commands;

use App\Models\VietnamCity;
use App\Models\WeatherData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckWeatherDataStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:check-status 
                            {--detailed : Hiển thị chi tiết từng tỉnh thành}
                            {--missing : Chỉ hiển thị tỉnh thành thiếu dữ liệu}
                            {--outdated : Chỉ hiển thị tỉnh thành có dữ liệu cũ (trên 6 giờ)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra tình trạng dữ liệu thời tiết của các tỉnh thành';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Kiểm tra tình trạng dữ liệu thời tiết...');
        $this->newLine();

        // Thống kê tổng quan
        $this->showOverallStats();

        if ($this->option('missing')) {
            $this->showMissingCities();
        } elseif ($this->option('outdated')) {
            $this->showOutdatedCities();
        } elseif ($this->option('detailed')) {
            $this->showDetailedStatus();
        } else {
            $this->showSummary();
        }

        return 0;
    }

    /**
     * Hiển thị thống kê tổng quan
     */
    protected function showOverallStats()
    {
        $totalCities = VietnamCity::active()->count();
        
        // Lấy danh sách city_code có dữ liệu thời tiết trong 24h và 6h
        $citiesWithWeather24h = WeatherData::recent(24)->pluck('city_code')->unique()->count();
        $citiesWithWeather6h = WeatherData::recent(6)->pluck('city_code')->unique()->count();
        
        $missingCities = $totalCities - $citiesWithWeather24h;
        $outdatedCities = $totalCities - $citiesWithWeather6h;

        $this->info('📊 THỐNG KÊ TỔNG QUAN:');
        $this->line("   • Tổng số tỉnh thành: <fg=blue>{$totalCities}</>");
        $this->line("   • Có dữ liệu thời tiết (24h): <fg=green>{$citiesWithWeather24h}</>");
        $this->line("   • Có dữ liệu thời tiết (6h): <fg=green>{$citiesWithWeather6h}</>");
        $this->line("   • Thiếu dữ liệu thời tiết: <fg=red>{$missingCities}</>");
        $this->line("   • Dữ liệu cũ (trên 6h): <fg=yellow>{$outdatedCities}</>");
        
        $coveragePercentage = $totalCities > 0 ? round(($citiesWithWeather24h / $totalCities) * 100, 2) : 0;
        $recentCoveragePercentage = $totalCities > 0 ? round(($citiesWithWeather6h / $totalCities) * 100, 2) : 0;
        
        $this->line("   • Tỷ lệ phủ sóng (24h): <fg=blue>{$coveragePercentage}%</>");
        $this->line("   • Tỷ lệ phủ sóng (6h): <fg=blue>{$recentCoveragePercentage}%</>");
        $this->newLine();
    }

    /**
     * Hiển thị tổng kết
     */
    protected function showSummary()
    {
        $missingCities = $this->getMissingCities();
        $outdatedCities = $this->getOutdatedCities();

        if ($missingCities->count() > 0) {
            $this->warn("⚠️  Có {$missingCities->count()} tỉnh thành thiếu dữ liệu thời tiết");
            $this->line("   Sử dụng: <fg=blue>php artisan weather:check-status --missing</> để xem chi tiết");
        }

        if ($outdatedCities->count() > 0) {
            $this->warn("⚠️  Có {$outdatedCities->count()} tỉnh thành có dữ liệu cũ");
            $this->line("   Sử dụng: <fg=blue>php artisan weather:check-status --outdated</> để xem chi tiết");
        }

        if ($missingCities->count() === 0 && $outdatedCities->count() === 0) {
            $this->info("✅ Tất cả tỉnh thành đều có dữ liệu thời tiết cập nhật!");
        }

        $this->newLine();
        $this->line("💡 Gợi ý:");
        $this->line("   • Cập nhật tất cả: <fg=blue>php artisan weather:update --all</>");
        $this->line("   • Cập nhật tỉnh thiếu: <fg=blue>php artisan weather:update</>");
        $this->line("   • Xem chi tiết: <fg=blue>php artisan weather:check-status --detailed</>");
    }

    /**
     * Hiển thị chi tiết tình trạng
     */
    protected function showDetailedStatus()
    {
        $cities = VietnamCity::active()->orderBy('region')->orderBy('name')->get();
        
        $this->info('📋 CHI TIẾT TÌNH TRẠNG DỮ LIỆU THỜI TIẾT:');
        $this->newLine();

        $headers = ['Tỉnh/Thành', 'Vùng', 'Trạng thái', 'Nhiệt độ', 'Cập nhật lúc'];
        $rows = [];

        foreach ($cities as $city) {
            $latestWeather = $city->latestWeatherData;
            
            if ($latestWeather) {
                $status = $this->getStatusIcon($latestWeather->last_updated);
                $temperature = $latestWeather->temperature . '°C';
                $updatedAt = $latestWeather->last_updated->format('H:i d/m/Y');
            } else {
                $status = '<fg=red>❌</>';
                $temperature = 'N/A';
                $updatedAt = 'N/A';
            }

            $rows[] = [
                $city->name,
                $city->region,
                $status,
                $temperature,
                $updatedAt
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Hiển thị tỉnh thành thiếu dữ liệu
     */
    protected function showMissingCities()
    {
        $missingCities = $this->getMissingCities();

        if ($missingCities->count() === 0) {
            $this->info("✅ Không có tỉnh thành nào thiếu dữ liệu thời tiết!");
            return;
        }

        $this->warn("❌ DANH SÁCH TỈNH THÀNH THIẾU DỮ LIỆU THỜI TIẾT:");
        $this->newLine();

        $headers = ['Tỉnh/Thành', 'Vùng', 'Mã', 'Tọa độ'];
        $rows = [];

        foreach ($missingCities as $city) {
            $rows[] = [
                $city->name,
                $city->region,
                $city->code,
                "{$city->latitude}, {$city->longitude}"
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
        $this->line("💡 Chạy lệnh sau để cập nhật: <fg=blue>php artisan weather:update --all</>");
    }

    /**
     * Hiển thị tỉnh thành có dữ liệu cũ
     */
    protected function showOutdatedCities()
    {
        $outdatedCities = $this->getOutdatedCities();

        if ($outdatedCities->count() === 0) {
            $this->info("✅ Không có tỉnh thành nào có dữ liệu cũ!");
            return;
        }

        $this->warn("⚠️  DANH SÁCH TỈNH THÀNH CÓ DỮ LIỆU CŨ (trên 6 giờ):");
        $this->newLine();

        $headers = ['Tỉnh/Thành', 'Vùng', 'Nhiệt độ', 'Cập nhật lúc', 'Thời gian cũ'];
        $rows = [];

        foreach ($outdatedCities as $city) {
            $latestWeather = $city->latestWeatherData;
            $hoursOld = now()->diffInHours($latestWeather->last_updated);
            
            $rows[] = [
                $city->name,
                $city->region,
                $latestWeather->temperature . '°C',
                $latestWeather->last_updated->format('H:i d/m/Y'),
                $hoursOld . ' giờ trước'
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
        $this->line("💡 Chạy lệnh sau để cập nhật: <fg=blue>php artisan weather:update</>");
    }

    /**
     * Lấy danh sách tỉnh thành thiếu dữ liệu
     */
    protected function getMissingCities()
    {
        $citiesWithWeather = WeatherData::recent(24)->pluck('city_code')->unique()->toArray();
        
        return VietnamCity::active()
            ->whereNotIn('code', $citiesWithWeather)
            ->orderBy('region')
            ->orderBy('name')
            ->get();
    }

    /**
     * Lấy danh sách tỉnh thành có dữ liệu cũ
     */
    protected function getOutdatedCities()
    {
        $citiesWithRecentWeather = WeatherData::recent(6)->pluck('city_code')->unique()->toArray();
        
        return VietnamCity::active()
            ->whereNotIn('code', $citiesWithRecentWeather)
            ->whereHas('weatherData')
            ->orderBy('region')
            ->orderBy('name')
            ->get();
    }

    /**
     * Lấy icon trạng thái dựa trên thời gian cập nhật
     */
    protected function getStatusIcon($lastUpdated)
    {
        $hoursOld = now()->diffInHours($lastUpdated);
        
        if ($hoursOld <= 1) {
            return '<fg=green>✅</>'; // Mới (dưới 1 giờ)
        } elseif ($hoursOld <= 6) {
            return '<fg=yellow>⚠️</>'; // Cũ (1-6 giờ)
        } else {
            return '<fg=red>❌</>'; // Rất cũ (trên 6 giờ)
        }
    }
} 