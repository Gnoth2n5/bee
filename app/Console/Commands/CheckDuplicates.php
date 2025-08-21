<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckDuplicates extends Command
{
    protected $signature = 'recipes:check-duplicates';
    protected $description = 'Kiểm tra trùng lặp trong recipes_data.json';

    public function handle()
    {
        $jsonPath = storage_path('app/recipes_data.json');

        if (!file_exists($jsonPath)) {
            $this->error('File recipes_data.json không tồn tại!');
            return 1;
        }

        $this->info('Đang đọc file JSON...');
        $jsonData = file_get_contents($jsonPath);
        $recipes = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Lỗi khi parse JSON: ' . json_last_error_msg());
            return 1;
        }

        $this->info('Kiểm tra trùng lặp trong recipes_data.json...');
        $this->line('');

        $titles = [];
        $duplicates = [];
        $totalRecipes = count($recipes);

        $this->line("Tổng số recipes: {$totalRecipes}");
        $this->line('');

        foreach ($recipes as $index => $recipe) {
            $title = trim(strtolower($recipe['title']));

            if (in_array($title, $titles)) {
                $duplicates[] = [
                    'index' => $index,
                    'title' => $recipe['title']
                ];
                $this->warn("Tìm thấy trùng lặp: {$recipe['title']} (Index: {$index})");
            } else {
                $titles[] = $title;
            }
        }

        $this->line('');
        $this->line("Tổng số recipes trùng lặp: " . count($duplicates));
        $this->line("Số recipes sau khi loại bỏ trùng lặp: " . ($totalRecipes - count($duplicates)));

        if (count($duplicates) > 0) {
            $this->line('');
            $this->line('Danh sách các recipes trùng lặp:');
            foreach ($duplicates as $dup) {
                $this->line("- {$dup['title']} (Index: {$dup['index']})");
            }
        } else {
            $this->line('');
            $this->info('Không tìm thấy recipes trùng lặp nào!');
        }

        $this->line('');
        $this->info('Hoàn thành!');

        return 0;
    }
}
