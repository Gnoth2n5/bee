<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Categories chính theo loại món ăn
            ['name' => 'Món chính', 'slug' => 'mon-chinh', 'description' => 'Các món ăn chính trong bữa ăn', 'icon' => 'heroicon-o-home', 'sort_order' => 1],
            ['name' => 'Món phụ', 'slug' => 'mon-phu', 'description' => 'Các món ăn phụ, món khai vị', 'icon' => 'heroicon-o-star', 'sort_order' => 2],
            ['name' => 'Món chay', 'slug' => 'mon-chay', 'description' => 'Các món ăn chay', 'icon' => 'heroicon-o-heart', 'sort_order' => 3],
            ['name' => 'Tráng miệng', 'slug' => 'trang-mieng', 'description' => 'Các món tráng miệng, bánh kẹo', 'icon' => 'heroicon-o-cake', 'sort_order' => 4],
            ['name' => 'Đồ uống', 'slug' => 'do-uong', 'description' => 'Các loại đồ uống, nước giải khát', 'icon' => 'heroicon-o-glass', 'sort_order' => 5],
            
            // Categories theo phương pháp nấu
            ['name' => 'Món nướng', 'slug' => 'mon-nuong', 'description' => 'Các món được nướng', 'icon' => 'heroicon-o-fire', ''#DC2626', 'sort_order' => 6],
            ['name' => 'Món chiên', 'slug' => 'mon-chien', 'description' => 'Các món được chiên', 'icon' => 'heroicon-o-bolt', 'sort_order' => 7],
            ['name' => 'Món xào', 'slug' => 'mon-xao', 'description' => 'Các món được xào', 'icon' => 'heroicon-o-sparkles', ''#EF4444', 'sort_order' => 8],
            ['name' => 'Món luộc', 'slug' => 'mon-luoc', 'description' => 'Các món được luộc', 'icon' => 'heroicon-o-droplet', ''#3B82F6', 'sort_order' => 9],
            ['name' => 'Món hấp', 'slug' => 'mon-hap', 'description' => 'Các món được hấp', 'icon' => 'heroicon-o-cloud', ''#06B6D4', 'sort_order' => 10],
            
            // Categories theo nguyên liệu chính
            ['name' => 'Món thịt', 'slug' => 'mon-thit', 'description' => 'Các món với thịt là nguyên liệu chính', 'icon' => 'heroicon-o-cube', ''#7C2D12', 'sort_order' => 11],
            ['name' => 'Món cá', 'slug' => 'mon-ca', 'description' => 'Các món với cá là nguyên liệu chính', 'icon' => 'heroicon-o-fish', ''#2196F3', 'sort_order' => 12],
            ['name' => 'Món tôm', 'slug' => 'mon-tom', 'description' => 'Các món với tôm là nguyên liệu chính', 'icon' => 'heroicon-o-bug', ''#FF5722', 'sort_order' => 13],
            ['name' => 'Món rau củ', 'slug' => 'mon-rau-cu', 'description' => 'Các món với rau củ là nguyên liệu chính', 'icon' => 'heroicon-o-leaf', ''#4CAF50', 'sort_order' => 14],
            ['name' => 'Món trứng', 'slug' => 'mon-trung', 'description' => 'Các món với trứng là nguyên liệu chính', 'icon' => 'heroicon-o-egg', ''#FFC107', 'sort_order' => 15],
            
            // Categories theo bữa ăn
            ['name' => 'Bữa sáng', 'slug' => 'bua-sang', 'description' => 'Các món ăn cho bữa sáng', 'icon' => 'heroicon-o-sun', ''#FF9800', 'sort_order' => 16],
            ['name' => 'Bữa trưa', 'slug' => 'bua-trua', 'description' => 'Các món ăn cho bữa trưa', 'icon' => 'heroicon-o-clock', ''#FF5722', 'sort_order' => 17],
            ['name' => 'Bữa tối', 'slug' => 'bua-toi', 'description' => 'Các món ăn cho bữa tối', 'icon' => 'heroicon-o-moon', ''#673AB7', 'sort_order' => 18],
            ['name' => 'Món tiệc', 'slug' => 'mon-tiec', 'description' => 'Các món ăn cho tiệc, liên hoan', 'icon' => 'heroicon-o-gift', ''#E91E63', 'sort_order' => 19],
            
            // Categories theo mùa và thời tiết
            ['name' => 'Món mùa hè', 'slug' => 'mon-mua-he', 'description' => 'Các món ăn phù hợp mùa hè', 'icon' => 'heroicon-o-sun', ''#00BCD4', 'sort_order' => 20],
            ['name' => 'Món mùa đông', 'slug' => 'mon-mua-dong', 'description' => 'Các món ăn phù hợp mùa đông', 'icon' => 'heroicon-o-snowflake', ''#607D8B', 'sort_order' => 21],
            ['name' => 'Món mát', 'slug' => 'mon-mat', 'description' => 'Các món ăn mát, giải nhiệt', 'icon' => 'heroicon-o-snowflake', ''#06B6D4', 'sort_order' => 22],
            ['name' => 'Món nóng', 'slug' => 'mon-nong', 'description' => 'Các món ăn nóng, ấm', 'icon' => 'heroicon-o-fire', ''#B91C1C', 'sort_order' => 23],
            
            // Categories theo độ khó
            ['name' => 'Món dễ làm', 'slug' => 'mon-de-lam', 'description' => 'Các món ăn dễ thực hiện', 'icon' => 'heroicon-o-thumb-up', ''#4CAF50', 'sort_order' => 24],
            ['name' => 'Món trung bình', 'slug' => 'mon-trung-binh', 'description' => 'Các món ăn có độ khó trung bình', 'icon' => 'heroicon-o-minus', ''#FF9800', 'sort_order' => 25],
            ['name' => 'Món khó', 'slug' => 'mon-kho', 'description' => 'Các món ăn khó thực hiện', 'icon' => 'heroicon-o-star', ''#F44336', 'sort_order' => 26],
            
            // Categories theo thời gian
            ['name' => 'Món nhanh', 'slug' => 'mon-nhanh', 'description' => 'Các món ăn nấu nhanh', 'icon' => 'heroicon-o-bolt', ''#2196F3', 'sort_order' => 27],
            ['name' => 'Món cầu kỳ', 'slug' => 'mon-cau-ky', 'description' => 'Các món ăn cầu kỳ, mất thời gian', 'icon' => 'heroicon-o-clock', ''#9C27B0', 'sort_order' => 28],
            
            // Categories theo vùng miền
            ['name' => 'Món miền Bắc', 'slug' => 'mon-mien-bac', 'description' => 'Các món ăn đặc trưng miền Bắc', 'icon' => 'heroicon-o-map-pin', ''#3B82F6', 'sort_order' => 29],
            ['name' => 'Món miền Trung', 'slug' => 'mon-mien-trung', 'description' => 'Các món ăn đặc trưng miền Trung', 'icon' => 'heroicon-o-map-pin', 'sort_order' => 30],
            ['name' => 'Món miền Nam', 'slug' => 'mon-mien-nam', 'description' => 'Các món ăn đặc trưng miền Nam', 'icon' => 'heroicon-o-map-pin', ''#10B981', 'sort_order' => 31],
            
            // Categories theo loại món cụ thể
            ['name' => 'Bún', 'slug' => 'bun', 'description' => 'Các món bún', 'icon' => 'heroicon-o-beaker', ''#3B82F6', 'sort_order' => 32],
            ['name' => 'Phở', 'slug' => 'pho', 'description' => 'Các món phở', 'icon' => 'heroicon-o-beaker', ''#EF4444', 'sort_order' => 33],
            ['name' => 'Bánh mì', 'slug' => 'banh-mi', 'description' => 'Các loại bánh mì', 'icon' => 'heroicon-o-bread', 'sort_order' => 34],
            ['name' => 'Bánh', 'slug' => 'banh', 'description' => 'Các loại bánh', 'icon' => 'heroicon-o-cake', 'sort_order' => 35],
            ['name' => 'Chè', 'slug' => 'che', 'description' => 'Các loại chè', 'icon' => 'heroicon-o-glass', 'sort_order' => 36],
            ['name' => 'Canh', 'slug' => 'canh', 'description' => 'Các món canh', 'icon' => 'heroicon-o-beaker', ''#10B981', 'sort_order' => 37],
            ['name' => 'Súp', 'slug' => 'sup', 'description' => 'Các món súp', 'icon' => 'heroicon-o-beaker', ''#06B6D4', 'sort_order' => 38],
            ['name' => 'Cháo', 'slug' => 'chao', 'description' => 'Các món cháo', 'icon' => 'heroicon-o-mug-hot', ''#F97316', 'sort_order' => 39],
            ['name' => 'Lẩu', 'slug' => 'lau', 'description' => 'Các món lẩu', 'icon' => 'heroicon-o-fire', ''#DC2626', 'sort_order' => 40],
            ['name' => 'Salad', 'slug' => 'salad', 'description' => 'Các món salad', 'icon' => 'heroicon-o-leaf', ''#10B981', 'sort_order' => 41],
            ['name' => 'Cơm', 'slug' => 'com', 'description' => 'Các món cơm', 'icon' => 'heroicon-o-home', 'sort_order' => 42],
            ['name' => 'Xôi', 'slug' => 'xoi', 'description' => 'Các món xôi', 'icon' => 'heroicon-o-home', 'sort_order' => 43],
            
            // Categories theo ẩm thực quốc tế
            ['name' => 'Món Việt Nam', 'slug' => 'mon-viet-nam', 'description' => 'Các món ăn Việt Nam', 'icon' => 'heroicon-o-flag', ''#FF5722', 'sort_order' => 44],
            ['name' => 'Món châu Á', 'slug' => 'mon-chau-a', 'description' => 'Các món ăn châu Á', 'icon' => 'heroicon-o-globe-asia-australia', ''#795548', 'sort_order' => 45],
            ['name' => 'Món châu Âu', 'slug' => 'mon-chau-au', 'description' => 'Các món ăn châu Âu', 'icon' => 'heroicon-o-globe-europe-africa', ''#607D8B', 'sort_order' => 46],
            ['name' => 'Món Mỹ', 'slug' => 'mon-my', 'description' => 'Các món ăn Mỹ', 'icon' => 'heroicon-o-globe-americas', ''#3F51B5', 'sort_order' => 47],
            
            // Categories theo dinh dưỡng
            ['name' => 'Món ít calo', 'slug' => 'mon-it-calo', 'description' => 'Các món ăn ít calo', 'icon' => 'heroicon-o-heart', ''#E91E63', 'sort_order' => 48],
            ['name' => 'Món giàu protein', 'slug' => 'mon-giau-protein', 'description' => 'Các món ăn giàu protein', 'icon' => 'heroicon-o-muscle', ''#FF9800', 'sort_order' => 49],
            ['name' => 'Món giàu vitamin', 'slug' => 'mon-giau-vitamin', 'description' => 'Các món ăn giàu vitamin', 'icon' => 'heroicon-o-sparkles', ''#4CAF50', 'sort_order' => 50],
            
            // Categories theo dịp lễ
            ['name' => 'Món Tết', 'slug' => 'mon-tet', 'description' => 'Các món ăn truyền thống ngày Tết', 'icon' => 'heroicon-o-gift', ''#F44336', 'sort_order' => 51],
            ['name' => 'Món lễ hội', 'slug' => 'mon-le-hoi', 'description' => 'Các món ăn cho lễ hội', 'icon' => 'heroicon-o-party-popper', ''#E91E63', 'sort_order' => 52],
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
