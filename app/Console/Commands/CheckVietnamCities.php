<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class CheckVietnamCities extends Command
{
    protected $signature = 'vietnam:check-cities';
    protected $description = 'Kiểm tra dữ liệu thành phố Việt Nam trong database';

    public function handle()
    {
        $this->info('🔍 Kiểm tra dữ liệu thành phố Việt Nam...');

        $totalCities = VietnamCity::count();
        $this->info("📊 Tổng số thành phố: {$totalCities}");

        if ($totalCities > 0) {
            $this->info('📋 10 thành phố đầu tiên:');
            $cities = VietnamCity::take(10)->get();
            
            foreach ($cities as $city) {
                $status = $city->is_active ? '✅' : '❌';
                $this->info("   {$status} {$city->name} (Code: {$city->code}, Region: {$city->region})");
            }

            // Thống kê theo vùng miền
            $regions = VietnamCity::selectRaw('region, COUNT(*) as count')
                ->groupBy('region')
                ->pluck('count', 'region')
                ->toArray();

            $this->newLine();
            $this->info('📈 Thống kê theo vùng miền:');
            foreach ($regions as $region => $count) {
                $this->info("   📍 {$region}: {$count} thành phố");
            }

            // Kiểm tra dữ liệu API
            $withApiData = VietnamCity::whereNotNull('api_data')->count();
            $this->newLine();
            $this->info("🔗 Có dữ liệu API: {$withApiData}/{$totalCities}");
        } else {
            $this->warn('⚠️  Không có dữ liệu thành phố nào trong database');
        }

        return 0;
    }
} 