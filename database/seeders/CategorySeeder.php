<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Categories cho thời tiết nóng và độ ẩm cao (>=24°C, >70%)
            ['name' => 'Súp', 'slug' => 'sup', 'description' => 'Các món súp mát, nhẹ', 'icon' => 'heroicon-o-beaker', 'color' => '#3B82F6', 'sort_order' => 1],
            ['name' => 'Salad', 'slug' => 'salad', 'description' => 'Các món salad tươi mát', 'icon' => 'heroicon-o-leaf', 'color' => '#10B981', 'sort_order' => 2],
            ['name' => 'Món mát', 'slug' => 'mon-mat', 'description' => 'Các món ăn mát, giải nhiệt', 'icon' => 'heroicon-o-snowflake', 'color' => '#06B6D4', 'sort_order' => 3],
            ['name' => 'Tráng miệng', 'slug' => 'trang-mieng', 'description' => 'Các món tráng miệng mát', 'icon' => 'heroicon-o-cake', 'color' => '#F59E0B', 'sort_order' => 4],
            ['name' => 'Đồ uống', 'slug' => 'do-uong', 'description' => 'Các loại đồ uống mát', 'icon' => 'heroicon-o-glass', 'color' => '#8B5CF6', 'sort_order' => 5],

            // Categories cho thời tiết nóng và độ ẩm thấp (>=24°C, <=70%)
            ['name' => 'Canh', 'slug' => 'canh', 'description' => 'Các món canh nước', 'icon' => 'heroicon-o-fire', 'color' => '#EF4444', 'sort_order' => 6],
            ['name' => 'Món nước', 'slug' => 'mon-nuoc', 'description' => 'Các món có nhiều nước', 'icon' => 'heroicon-o-droplet', 'color' => '#3B82F6', 'sort_order' => 7],
            ['name' => 'Cháo', 'slug' => 'chao', 'description' => 'Các món cháo', 'icon' => 'heroicon-o-mug-hot', 'color' => '#F97316', 'sort_order' => 8],

            // Categories cho thời tiết lạnh (<15°C)
            ['name' => 'Lẩu', 'slug' => 'lau', 'description' => 'Các món lẩu nóng', 'icon' => 'heroicon-o-fire', 'color' => '#DC2626', 'sort_order' => 9],
            ['name' => 'Súp nóng', 'slug' => 'sup-nong', 'description' => 'Các món súp nóng', 'icon' => 'heroicon-o-beaker', 'color' => '#EA580C', 'sort_order' => 10],
            ['name' => 'Món nóng', 'slug' => 'mon-nong', 'description' => 'Các món ăn nóng', 'icon' => 'heroicon-o-fire', 'color' => '#B91C1C', 'sort_order' => 11],
            ['name' => 'Thịt', 'slug' => 'thit', 'description' => 'Các món thịt giàu dinh dưỡng', 'icon' => 'heroicon-o-cube', 'color' => '#7C2D12', 'sort_order' => 12],

            // Categories chung
            ['name' => 'Món chính', 'slug' => 'mon-chinh', 'description' => 'Các món ăn chính', 'icon' => 'heroicon-o-home', 'color' => '#059669', 'sort_order' => 13],
            ['name' => 'Món phụ', 'slug' => 'mon-phu', 'description' => 'Các món ăn phụ', 'icon' => 'heroicon-o-star', 'color' => '#D97706', 'sort_order' => 14],
            ['name' => 'Món chay', 'slug' => 'mon-chay', 'description' => 'Các món ăn chay', 'icon' => 'heroicon-o-heart', 'color' => '#059669', 'sort_order' => 15],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('✅ Categories seeded successfully!');
    }
}
