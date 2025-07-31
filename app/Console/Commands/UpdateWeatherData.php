<?php

namespace App\Console\Commands;

use App\Services\WeatherService;
use App\Services\WeatherRecipeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateWeatherData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:update 
                            {--city= : Update weather for specific city code}
                            {--all : Update weather for all cities}
                            {--suggestions : Generate recipe suggestions after weather update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update weather data from OpenWeatherMap API';

    protected $weatherService;
    protected $weatherRecipeService;

    /**
     * Create a new command instance.
     */
    public function __construct(WeatherService $weatherService, WeatherRecipeService $weatherRecipeService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
        $this->weatherRecipeService = $weatherRecipeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Bắt đầu cập nhật dữ liệu thời tiết...');

        try {
            if ($this->option('city')) {
                $this->updateSpecificCity($this->option('city'));
            } elseif ($this->option('all')) {
                $this->updateAllCities();
            } else {
                $this->updateOutdatedCities();
            }

            if ($this->option('suggestions')) {
                $this->generateSuggestions();
            }

            $this->info('✅ Cập nhật dữ liệu thời tiết hoàn tất!');

        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi cập nhật dữ liệu thời tiết: ' . $e->getMessage());
            Log::error('Weather update command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Update weather for a specific city.
     */
    protected function updateSpecificCity($cityCode)
    {
        $city = \App\Models\VietnamCity::findByCode($cityCode);

        if (!$city) {
            $this->error("❌ Không tìm thấy thành phố với mã: {$cityCode}");
            return;
        }

        $this->info("🌤️  Đang cập nhật thời tiết cho {$city->name}...");

        $weatherData = $this->weatherService->getCurrentWeather($city);

        if ($weatherData) {
            $this->info("✅ Cập nhật thành công: {$weatherData['temperature']}°C, {$weatherData['weather_description']}");
        } else {
            $this->warn("⚠️  Không thể cập nhật thời tiết cho {$city->name}");
        }
    }

    /**
     * Update weather for all cities.
     */
    protected function updateAllCities()
    {
        $cities = \App\Models\VietnamCity::active()->get();
        $totalCities = $cities->count();

        $this->info("🌍 Đang cập nhật thời tiết cho {$totalCities} thành phố...");

        $progressBar = $this->output->createProgressBar($totalCities);
        $progressBar->start();

        $updatedCount = 0;

        foreach ($cities as $city) {
            $weatherData = $this->weatherService->getCurrentWeather($city);
            if ($weatherData) {
                $updatedCount++;
            }

            $progressBar->advance();

            // Add delay to avoid rate limiting
            usleep(100000); // 0.1 second delay
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("✅ Đã cập nhật thành công {$updatedCount}/{$totalCities} thành phố");
    }

    /**
     * Update weather for cities with outdated data.
     */
    protected function updateOutdatedCities()
    {
        $outdatedCities = $this->weatherService->getCitiesWithOutdatedWeather(6);
        $totalOutdated = $outdatedCities->count();

        if ($totalOutdated === 0) {
            $this->info("✅ Tất cả dữ liệu thời tiết đều đã được cập nhật gần đây");
            return;
        }

        $this->info("🔄 Đang cập nhật thời tiết cho {$totalOutdated} thành phố có dữ liệu cũ...");

        $progressBar = $this->output->createProgressBar($totalOutdated);
        $progressBar->start();

        $updatedCount = 0;

        foreach ($outdatedCities as $city) {
            $weatherData = $this->weatherService->getCurrentWeather($city);
            if ($weatherData) {
                $updatedCount++;
            }

            $progressBar->advance();

            // Add delay to avoid rate limiting
            usleep(100000); // 0.1 second delay
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("✅ Đã cập nhật thành công {$updatedCount}/{$totalOutdated} thành phố");
    }

    /**
     * Generate recipe suggestions based on weather.
     */
    protected function generateSuggestions()
    {
        $this->info("🍽️  Đang tạo đề xuất món ăn theo thời tiết...");

        $generatedCount = $this->weatherRecipeService->generateAllCitiesSuggestions();

        $this->info("✅ Đã tạo đề xuất cho {$generatedCount} thành phố");
    }
}