<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class ListAllCities extends Command
{
    protected $signature = 'vietnam:list-cities {--region= : Miền cụ thể (north/central/south)}';
    protected $description = 'Liệt kê tất cả tỉnh thành có tọa độ';

    public function handle()
    {
        $region = $this->option('region');

        $this->info('🗺️  Danh sách tất cả tỉnh thành có tọa độ:');

        $query = VietnamCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0.0)
            ->where('longitude', '!=', 0.0)
            ->orderBy('name');

        // Lọc theo miền nếu được chỉ định
        if ($region) {
            $query = $this->filterByRegion($query, $region);
            $this->info("📍 Miền: " . ucfirst($region));
        }

        $cities = $query->get();

        $this->info("📊 Tổng số: {$cities->count()} tỉnh");
        $this->newLine();

        foreach ($cities as $index => $city) {
            $number = $index + 1;
            $regionName = $this->getRegionName($city->latitude);
            $this->info("{$number}. {$city->name}");
            $this->info("   📍 Tọa độ: {$city->latitude}, {$city->longitude}");
            $this->info("   🌍 Miền: {$regionName}");
            $this->newLine();
        }

        return 0;
    }

    protected function filterByRegion($query, $region)
    {
        switch (strtolower($region)) {
            case 'north':
                return $query->where('latitude', '>=', 20.0);
            case 'central':
                return $query->whereBetween('latitude', [15.0, 20.0]);
            case 'south':
                return $query->where('latitude', '<', 15.0);
            default:
                return $query;
        }
    }

    protected function getRegionName($latitude)
    {
        if ($latitude >= 20.0) {
            return 'Miền Bắc';
        } elseif ($latitude >= 15.0) {
            return 'Miền Trung';
        } else {
            return 'Miền Nam';
        }
    }
}