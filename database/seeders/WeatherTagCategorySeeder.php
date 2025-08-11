<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WeatherTagCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo thêm categories cho hệ thống đề xuất thời tiết
        $additionalCategories = [
            [
                'name' => 'Món khô',
                'slug' => 'mon-kho',
                'description' => 'Các món ăn khô, ít nước',
                'icon' => 'heroicon-o-fire',
                'color' => '#DC2626',
                'sort_order' => 11
            ],
            [
                'name' => 'Món cay',
                'slug' => 'mon-cay',
                'description' => 'Các món ăn có vị cay',
                'icon' => 'heroicon-o-fire',
                'color' => '#EA580C',
                'sort_order' => 12
            ],
            [
                'name' => 'Nướng',
                'slug' => 'nuong',
                'description' => 'Các món nướng thơm ngon',
                'icon' => 'heroicon-o-fire',
                'color' => '#F97316',
                'sort_order' => 13
            ],
            [
                'name' => 'Chiên',
                'slug' => 'chien',
                'description' => 'Các món chiên giòn',
                'icon' => 'heroicon-o-fire',
                'color' => '#F59E0B',
                'sort_order' => 14
            ],
            [
                'name' => 'Món chính',
                'slug' => 'mon-chinh',
                'description' => 'Các món ăn chính',
                'icon' => 'heroicon-o-home',
                'color' => '#059669',
                'sort_order' => 15
            ],
            [
                'name' => 'Món nóng',
                'slug' => 'mon-nong',
                'description' => 'Các món ăn nóng',
                'icon' => 'heroicon-o-fire',
                'color' => '#DC2626',
                'sort_order' => 16
            ],
            [
                'name' => 'Thịt',
                'slug' => 'thit',
                'description' => 'Các món ăn từ thịt',
                'icon' => 'heroicon-o-home',
                'color' => '#7C3AED',
                'sort_order' => 17
            ],
            [
                'name' => 'Hải sản',
                'slug' => 'hai-san',
                'description' => 'Các món ăn từ hải sản',
                'icon' => 'heroicon-o-home',
                'color' => '#0EA5E9',
                'sort_order' => 18
            ]
        ];

        foreach ($additionalCategories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Tạo thêm tags cho hệ thống đề xuất thời tiết
        $additionalTags = [
            // Tags cho món ăn mát
            ['name' => 'mát', 'slug' => 'mat', 'description' => 'Món ăn mát', 'color' => '#0EA5E9'],
            ['name' => 'lạnh', 'slug' => 'lanh', 'description' => 'Món ăn lạnh', 'color' => '#06B6D4'],
            ['name' => 'giải nhiệt', 'slug' => 'giai-nhiet', 'description' => 'Món ăn giải nhiệt', 'color' => '#3B82F6'],
            ['name' => 'tươi', 'slug' => 'tuoi', 'description' => 'Món ăn tươi', 'color' => '#10B981'],
            ['name' => 'nhẹ', 'slug' => 'nhe', 'description' => 'Món ăn nhẹ', 'color' => '#84CC16'],
            ['name' => 'dễ tiêu', 'slug' => 'de-tieu', 'description' => 'Món ăn dễ tiêu', 'color' => '#22C55E'],

            // Tags cho món ăn nóng
            ['name' => 'nóng', 'slug' => 'nong', 'description' => 'Món ăn nóng', 'color' => '#EF4444'],
            ['name' => 'ấm', 'slug' => 'am', 'description' => 'Món ăn ấm', 'color' => '#F97316'],
            ['name' => 'dinh dưỡng', 'slug' => 'dinh-duong', 'description' => 'Món ăn dinh dưỡng', 'color' => '#8B5CF6'],
            ['name' => 'bổ dưỡng', 'slug' => 'bo-duong', 'description' => 'Món ăn bổ dưỡng', 'color' => '#EC4899'],

            // Tags cho món ăn có nước
            ['name' => 'nước', 'slug' => 'nuoc', 'description' => 'Món ăn có nước', 'color' => '#0EA5E9'],
            ['name' => 'canh', 'slug' => 'canh', 'description' => 'Món canh', 'color' => '#3B82F6'],
            ['name' => 'súp', 'slug' => 'sup', 'description' => 'Món súp', 'color' => '#06B6D4'],
            ['name' => 'cháo', 'slug' => 'chao', 'description' => 'Món cháo', 'color' => '#84CC16'],

            // Tags cho món ăn khô
            ['name' => 'khô', 'slug' => 'kho', 'description' => 'Món ăn khô', 'color' => '#F59E0B'],
            ['name' => 'cay', 'slug' => 'cay', 'description' => 'Món ăn cay', 'color' => '#DC2626'],
            ['name' => 'nướng', 'slug' => 'nuong', 'description' => 'Món nướng', 'color' => '#EA580C'],
            ['name' => 'chiên', 'slug' => 'chien', 'description' => 'Món chiên', 'color' => '#F97316'],
            ['name' => 'đậm đà', 'slug' => 'dam-da', 'description' => 'Món ăn đậm đà', 'color' => '#7C2D12'],

            // Tags cho món ăn cân bằng
            ['name' => 'cân bằng', 'slug' => 'can-bang', 'description' => 'Món ăn cân bằng', 'color' => '#059669'],
            ['name' => 'đa dạng', 'slug' => 'da-dang', 'description' => 'Món ăn đa dạng', 'color' => '#8B5CF6'],
            ['name' => 'ngon', 'slug' => 'ngon', 'description' => 'Món ăn ngon', 'color' => '#EC4899'],

            // Tags cho món ăn đặc biệt
            ['name' => 'lẩu', 'slug' => 'lau', 'description' => 'Món lẩu', 'color' => '#DC2626'],
            ['name' => 'mùa hè', 'slug' => 'mua-he', 'description' => 'Món ăn mùa hè', 'color' => '#F59E0B'],
            ['name' => 'mùa đông', 'slug' => 'mua-dong', 'description' => 'Món ăn mùa đông', 'color' => '#0EA5E9'],
            ['name' => 'mùa mưa', 'slug' => 'mua-mua', 'description' => 'Món ăn mùa mưa', 'color' => '#3B82F6'],

            // Tags cho loại món ăn
            ['name' => 'truyền thống', 'slug' => 'truyen-thong', 'description' => 'Món ăn truyền thống', 'color' => '#7C2D12'],
            ['name' => 'hiện đại', 'slug' => 'hien-dai', 'description' => 'Món ăn hiện đại', 'color' => '#8B5CF6'],
            ['name' => 'dân dã', 'slug' => 'dan-da', 'description' => 'Món ăn dân dã', 'color' => '#059669'],
            ['name' => 'cao cấp', 'slug' => 'cao-cap', 'description' => 'Món ăn cao cấp', 'color' => '#EC4899'],
            ['name' => 'đường phố', 'slug' => 'duong-pho', 'description' => 'Món ăn đường phố', 'color' => '#F97316'],
            ['name' => 'gia đình', 'slug' => 'gia-dinh', 'description' => 'Món ăn gia đình', 'color' => '#10B981'],

            // Tags cho nguyên liệu
            ['name' => 'thịt bò', 'slug' => 'thit-bo', 'description' => 'Món ăn từ thịt bò', 'color' => '#7C2D12'],
            ['name' => 'thịt heo', 'slug' => 'thit-heo', 'description' => 'Món ăn từ thịt heo', 'color' => '#DC2626'],
            ['name' => 'thịt gà', 'slug' => 'thit-ga', 'description' => 'Món ăn từ thịt gà', 'color' => '#F59E0B'],
            ['name' => 'hải sản', 'slug' => 'hai-san', 'description' => 'Món ăn từ hải sản', 'color' => '#0EA5E9'],
            ['name' => 'rau củ', 'slug' => 'rau-cu', 'description' => 'Món ăn từ rau củ', 'color' => '#10B981'],
            ['name' => 'nấm', 'slug' => 'nam', 'description' => 'Món ăn từ nấm', 'color' => '#8B5CF6'],

            // Tags cho cách chế biến
            ['name' => 'luộc', 'slug' => 'luoc', 'description' => 'Món ăn luộc', 'color' => '#3B82F6'],
            ['name' => 'hấp', 'slug' => 'hap', 'description' => 'Món ăn hấp', 'color' => '#06B6D4'],
            ['name' => 'xào', 'slug' => 'xao', 'description' => 'Món ăn xào', 'color' => '#F97316'],
            ['name' => 'kho', 'slug' => 'kho', 'description' => 'Món ăn kho', 'color' => '#7C2D12'],
            ['name' => 'om', 'slug' => 'om', 'description' => 'Món ăn om', 'color' => '#DC2626'],

            // Tags cho độ khó
            ['name' => 'dễ làm', 'slug' => 'de-lam', 'description' => 'Món ăn dễ làm', 'color' => '#10B981'],
            ['name' => 'trung bình', 'slug' => 'trung-binh', 'description' => 'Món ăn độ khó trung bình', 'color' => '#F59E0B'],
            ['name' => 'khó làm', 'slug' => 'kho-lam', 'description' => 'Món ăn khó làm', 'color' => '#DC2626'],

            // Tags cho thời gian
            ['name' => 'nhanh', 'slug' => 'nhanh', 'description' => 'Món ăn nhanh', 'color' => '#10B981'],
            ['name' => 'trung bình', 'slug' => 'thoi-gian-trung-binh', 'description' => 'Thời gian nấu trung bình', 'color' => '#F59E0B'],
            ['name' => 'lâu', 'slug' => 'lau', 'description' => 'Món ăn nấu lâu', 'color' => '#DC2626'],

            // Tags cho bữa ăn
            ['name' => 'sáng', 'slug' => 'sang', 'description' => 'Món ăn sáng', 'color' => '#F59E0B'],
            ['name' => 'trưa', 'slug' => 'trua', 'description' => 'Món ăn trưa', 'color' => '#10B981'],
            ['name' => 'tối', 'slug' => 'toi', 'description' => 'Món ăn tối', 'color' => '#3B82F6'],
            ['name' => 'tráng miệng', 'slug' => 'trang-mieng', 'description' => 'Món tráng miệng', 'color' => '#EC4899'],
            ['name' => 'ăn vặt', 'slug' => 'an-vat', 'description' => 'Món ăn vặt', 'color' => '#8B5CF6'],

            // Tags cho sức khỏe
            ['name' => 'tốt cho sức khỏe', 'slug' => 'tot-cho-suc-khoe', 'description' => 'Món ăn tốt cho sức khỏe', 'color' => '#10B981'],
            ['name' => 'ít calo', 'slug' => 'it-calo', 'description' => 'Món ăn ít calo', 'color' => '#84CC16'],
            ['name' => 'giàu protein', 'slug' => 'giau-protein', 'description' => 'Món ăn giàu protein', 'color' => '#8B5CF6'],
            ['name' => 'giàu vitamin', 'slug' => 'giau-vitamin', 'description' => 'Món ăn giàu vitamin', 'color' => '#EC4899'],
            ['name' => 'tốt cho tim', 'slug' => 'tot-cho-tim', 'description' => 'Món ăn tốt cho tim mạch', 'color' => '#DC2626'],
            ['name' => 'tốt cho tiêu hóa', 'slug' => 'tot-cho-tieu-hoa', 'description' => 'Món ăn tốt cho tiêu hóa', 'color' => '#059669']
        ];

        foreach ($additionalTags as $tagData) {
            Tag::firstOrCreate(
                ['slug' => $tagData['slug']],
                $tagData
            );
        }

        $this->command->info('Đã tạo thành công:');
        $this->command->info('- ' . count($additionalCategories) . ' categories bổ sung cho hệ thống đề xuất thời tiết');
        $this->command->info('- ' . count($additionalTags) . ' tags bổ sung cho hệ thống đề xuất thời tiết');
        $this->command->info('');
        $this->command->info('Categories mới: Món khô, Món cay, Nướng, Chiên, Món chính, Món nóng, Thịt, Hải sản');
        $this->command->info('Tags mới bao gồm: mát, lạnh, nóng, ấm, cay, khô, nước, canh, súp, cháo, lẩu, và nhiều tags khác...');
    }
} 