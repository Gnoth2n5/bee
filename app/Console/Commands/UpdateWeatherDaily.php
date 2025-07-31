<?php

namespace App\Console\Commands;

use App\Services\WeatherService;
use App\Services\WeatherRecipeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateWeatherDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:daily-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update weather data and generate recipe suggestions daily at 6 AM';

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
        $this->info('🌅 Bắt đầu cập nhật thời tiết hàng ngày...');

        $startTime = now();

        try {
            // Step 1: Update weather data for all cities
            $this->info('🌤️  Đang cập nhật dữ liệu thời tiết...');
            $updatedCities = $this->weatherService->updateAllCitiesWeather();
            $this->info("✅ Đã cập nhật thời tiết cho {$updatedCities} thành phố");

            // Step 2: Generate recipe suggestions
            $this->info('🍽️  Đang tạo đề xuất món ăn theo thời tiết...');
            $generatedSuggestions = $this->weatherRecipeService->generateAllCitiesSuggestions();
            $this->info("✅ Đã tạo đề xuất cho {$generatedSuggestions} thành phố");

            // Step 3: Clean old data
            $this->info('🧹 Đang dọn dẹp dữ liệu cũ...');
            $cleanedWeather = $this->weatherService->cleanOldWeatherData(7);
            $cleanedSuggestions = $this->weatherRecipeService->cleanOldSuggestions(7);
            $this->info("✅ Đã dọn dẹp {$cleanedWeather} bản ghi thời tiết cũ và {$cleanedSuggestions} đề xuất cũ");

            // Step 4: Log completion
            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            $this->info("🎉 Hoàn tất cập nhật thời tiết hàng ngày trong {$duration} giây");

            Log::info('Daily weather update completed', [
                'updated_cities' => $updatedCities,
                'generated_suggestions' => $generatedSuggestions,
                'cleaned_weather_records' => $cleanedWeather,
                'cleaned_suggestion_records' => $cleanedSuggestions,
                'duration_seconds' => $duration,
                'completed_at' => now()
            ]);

        } catch (\Exception $e) {
            $this->error('❌ Lỗi khi cập nhật thời tiết hàng ngày: ' . $e->getMessage());

            Log::error('Daily weather update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'failed_at' => now()
            ]);

            return 1;
        }

        return 0;
    }
}