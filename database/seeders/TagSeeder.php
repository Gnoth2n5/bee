<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Difficulty tags
            ['name' => 'Dễ làm', 'slug' => 'de-lam', 'description' => 'Công thức đơn giản, dễ thực hiện', 'color' => '#4CAF50'],
            ['name' => 'Trung bình', 'slug' => 'trung-binh', 'description' => 'Công thức có độ khó trung bình', 'color' => '#FF9800'],
            ['name' => 'Khó', 'slug' => 'kho', 'description' => 'Công thức phức tạp, cần kỹ năng cao', 'color' => '#F44336'],
            
            // Time tags
            ['name' => 'Nhanh', 'slug' => 'nhanh', 'description' => 'Chuẩn bị và nấu nhanh', 'color' => '#2196F3'],
            ['name' => '30 phút', 'slug' => '30-phut', 'description' => 'Hoàn thành trong 30 phút', 'color' => '#00BCD4'],
            ['name' => '1 giờ', 'slug' => '1-gio', 'description' => 'Thời gian nấu khoảng 1 giờ', 'color' => '#9C27B0'],
            
            // Cuisine tags
            ['name' => 'Việt Nam', 'slug' => 'viet-nam', 'description' => 'Ẩm thực Việt Nam', 'color' => '#FF5722'],
            ['name' => 'Châu Á', 'slug' => 'chau-a', 'description' => 'Ẩm thực châu Á', 'color' => '#795548'],
            ['name' => 'Châu Âu', 'slug' => 'chau-au', 'description' => 'Ẩm thực châu Âu', 'color' => '#607D8B'],
            ['name' => 'Mỹ', 'slug' => 'my', 'description' => 'Ẩm thực Mỹ', 'color' => '#3F51B5'],
            
            // Dietary tags
            ['name' => 'Chay', 'slug' => 'chay', 'description' => 'Món ăn chay', 'color' => '#8BC34A'],
            ['name' => 'Thuần chay', 'slug' => 'thuan-chay', 'description' => 'Món ăn thuần chay', 'color' => '#4CAF50'],
            ['name' => 'Không gluten', 'slug' => 'khong-gluten', 'description' => 'Không chứa gluten', 'color' => '#FFC107'],
            ['name' => 'Ít calo', 'slug' => 'it-calo', 'description' => 'Món ăn ít calo', 'color' => '#E91E63'],
            
            // Occasion tags
            ['name' => 'Bữa sáng', 'slug' => 'bua-sang', 'description' => 'Món ăn cho bữa sáng', 'color' => '#FF9800'],
            ['name' => 'Bữa trưa', 'slug' => 'bua-trua', 'description' => 'Món ăn cho bữa trưa', 'color' => '#FF5722'],
            ['name' => 'Bữa tối', 'slug' => 'bua-toi', 'description' => 'Món ăn cho bữa tối', 'color' => '#673AB7'],
            ['name' => 'Tiệc', 'slug' => 'tiec', 'description' => 'Món ăn cho tiệc', 'color' => '#E91E63'],
            
            // Seasonal tags
            ['name' => 'Mùa hè', 'slug' => 'mua-he', 'description' => 'Món ăn phù hợp mùa hè', 'color' => '#00BCD4'],
            ['name' => 'Mùa đông', 'slug' => 'mua-dong', 'description' => 'Món ăn phù hợp mùa đông', 'color' => '#607D8B'],
            ['name' => 'Tết', 'slug' => 'tet', 'description' => 'Món ăn truyền thống ngày Tết', 'color' => '#F44336'],
            
            // Ingredient tags
            ['name' => 'Gà', 'slug' => 'ga', 'description' => 'Món ăn với thịt gà', 'color' => '#FF9800'],
            ['name' => 'Cá', 'slug' => 'ca', 'description' => 'Món ăn với cá', 'color' => '#2196F3'],
            ['name' => 'Tôm', 'slug' => 'tom', 'description' => 'Món ăn với tôm', 'color' => '#FF5722'],
            ['name' => 'Rau củ', 'slug' => 'rau-cu', 'description' => 'Món ăn với rau củ', 'color' => '#4CAF50'],
            ['name' => 'Trái cây', 'slug' => 'trai-cay', 'description' => 'Món ăn với trái cây', 'color' => '#E91E63'],
            
            // Cooking method tags
            ['name' => 'Luộc', 'slug' => 'luoc', 'description' => 'Phương pháp luộc', 'color' => '#9C27B0'],
            ['name' => 'Chiên', 'slug' => 'chien', 'description' => 'Phương pháp chiên', 'color' => '#FF9800'],
            ['name' => 'Nướng', 'slug' => 'nuong', 'description' => 'Phương pháp nướng', 'color' => '#795548'],
            ['name' => 'Hấp', 'slug' => 'hap', 'description' => 'Phương pháp hấp', 'color' => '#607D8B'],
            ['name' => 'Xào', 'slug' => 'xao', 'description' => 'Phương pháp xào', 'color' => '#FF5722'],
            
            // Special tags
            ['name' => 'Nổi tiếng', 'slug' => 'noi-tieng', 'description' => 'Món ăn nổi tiếng', 'color' => '#FFD700'],
            ['name' => 'Truyền thống', 'slug' => 'truyen-thong', 'description' => 'Món ăn truyền thống', 'color' => '#8B4513'],
            ['name' => 'Hiện đại', 'slug' => 'hien-dai', 'description' => 'Món ăn hiện đại', 'color' => '#00BCD4'],
            ['name' => 'Sáng tạo', 'slug' => 'sang-tao', 'description' => 'Món ăn sáng tạo', 'color' => '#9C27B0']
        ];

        foreach ($tags as $tagData) {
            Tag::updateOrCreate(
                ['slug' => $tagData['slug']],
                [
                    'name' => $tagData['name'],
                    'description' => $tagData['description'],
                    'color' => $tagData['color'],
                    'usage_count' => 0
                ]
            );
        }

        $this->command->info('Tags seeded successfully!');
    }
}
