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
            ['name' => 'Dễ làm', 'slug' => 'de-lam', 'description' => 'Công thức đơn giản, dễ thực hiện'],
            ['name' => 'Trung bình', 'slug' => 'trung-binh', 'description' => 'Công thức có độ khó trung bình'],
            ['name' => 'Khó', 'slug' => 'kho', 'description' => 'Công thức phức tạp, cần kỹ năng cao'],

            // Time tags
            ['name' => 'Nhanh', 'slug' => 'nhanh', 'description' => 'Chuẩn bị và nấu nhanh'],
            ['name' => '30 phút', 'slug' => '30-phut', 'description' => 'Hoàn thành trong 30 phút'],
            ['name' => '1 giờ', 'slug' => '1-gio', 'description' => 'Thời gian nấu khoảng 1 giờ'],

            // Cuisine tags
            ['name' => 'Việt Nam', 'slug' => 'viet-nam', 'description' => 'Ẩm thực Việt Nam'],
            ['name' => 'Châu Á', 'slug' => 'chau-a', 'description' => 'Ẩm thực châu Á'],
            ['name' => 'Châu Âu', 'slug' => 'chau-au', 'description' => 'Ẩm thực châu Âu'],
            ['name' => 'Mỹ', 'slug' => 'my', 'description' => 'Ẩm thực Mỹ'],

            // Dietary tags
            ['name' => 'Chay', 'slug' => 'chay', 'description' => 'Món ăn chay'],
            ['name' => 'Thuần chay', 'slug' => 'thuan-chay', 'description' => 'Món ăn thuần chay'],
            ['name' => 'Không gluten', 'slug' => 'khong-gluten', 'description' => 'Không chứa gluten'],
            ['name' => 'Ít calo', 'slug' => 'it-calo', 'description' => 'Món ăn ít calo'],

            // Occasion tags
            ['name' => 'Bữa sáng', 'slug' => 'bua-sang', 'description' => 'Món ăn cho bữa sáng'],
            ['name' => 'Bữa trưa', 'slug' => 'bua-trua', 'description' => 'Món ăn cho bữa trưa'],
            ['name' => 'Bữa tối', 'slug' => 'bua-toi', 'description' => 'Món ăn cho bữa tối'],
            ['name' => 'Tiệc', 'slug' => 'tiec', 'description' => 'Món ăn cho tiệc'],

            // Seasonal tags
            ['name' => 'Mùa hè', 'slug' => 'mua-he', 'description' => 'Món ăn phù hợp mùa hè'],
            ['name' => 'Mùa đông', 'slug' => 'mua-dong', 'description' => 'Món ăn phù hợp mùa đông'],
            ['name' => 'Tết', 'slug' => 'tet', 'description' => 'Món ăn truyền thống ngày Tết'],

            // Ingredient tags
            ['name' => 'Gà', 'slug' => 'ga', 'description' => 'Món ăn với thịt gà'],
            ['name' => 'Cá', 'slug' => 'ca', 'description' => 'Món ăn với cá'],
            ['name' => 'Tôm', 'slug' => 'tom', 'description' => 'Món ăn với tôm'],
            ['name' => 'Rau củ', 'slug' => 'rau-cu', 'description' => 'Món ăn với rau củ'],
            ['name' => 'Trái cây', 'slug' => 'trai-cay', 'description' => 'Món ăn với trái cây'],

            // Cooking method tags
            ['name' => 'Luộc', 'slug' => 'luoc', 'description' => 'Phương pháp luộc'],
            ['name' => 'Chiên', 'slug' => 'chien', 'description' => 'Phương pháp chiên'],
            ['name' => 'Nướng', 'slug' => 'nuong', 'description' => 'Phương pháp nướng'],
            ['name' => 'Hấp', 'slug' => 'hap', 'description' => 'Phương pháp hấp'],
            ['name' => 'Xào', 'slug' => 'xao', 'description' => 'Phương pháp xào'],

            // Special tags
            ['name' => 'Nổi tiếng', 'slug' => 'noi-tieng', 'description' => 'Món ăn nổi tiếng'],
            ['name' => 'Truyền thống', 'slug' => 'truyen-thong', 'description' => 'Món ăn truyền thống'],
            ['name' => 'Hiện đại', 'slug' => 'hien-dai', 'description' => 'Món ăn hiện đại'],
            ['name' => 'Sáng tạo', 'slug' => 'sang-tao', 'description' => 'Món ăn sáng tạo']
        ];

        foreach ($tags as $tagData) {
            Tag::updateOrCreate(
                ['slug' => $tagData['slug']],
                [
                    'name' => $tagData['name'],
                    'description' => $tagData['description'],
                    'usage_count' => 0
                ]
            );
        }

        $this->command->info('Tags seeded successfully!');
    }
}
