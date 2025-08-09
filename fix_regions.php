<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VietnamCity;

$affected = VietnamCity::where('region', 'Khác')->update(['region' => 'Nam']);
echo "Updated records: {$affected}\n";
