<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class ListCityNames extends Command
{
    protected $signature = 'cities:names';
    protected $description = 'Liệt kê tên các tỉnh thành';

    public function handle()
    {
        $this->info('📝 Danh sách tên các tỉnh thành:');
        $this->newLine();

        $cities = VietnamCity::orderBy('name')->get();

        foreach ($cities as $city) {
            $this->line("'{$city->name}',");
        }

        return 0;
    }
}
