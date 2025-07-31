<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class CheckCityCodes extends Command
{
    protected $signature = 'vietnam:check-codes {--name= : Tìm theo tên}';
    protected $description = 'Kiểm tra mã thành phố';

    public function handle()
    {
        $name = $this->option('name');

        if ($name) {
            $cities = VietnamCity::where('name', 'LIKE', "%{$name}%")->get();
        } else {
            $cities = VietnamCity::take(10)->get();
        }

        $this->info('📋 Danh sách mã thành phố:');
        $this->newLine();

        foreach ($cities as $city) {
            $this->info("   {$city->name} -> Mã: {$city->code}");
        }

        return 0;
    }
} 