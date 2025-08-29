<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecipeApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        $tags = Tag::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->error('Cần có users và categories trước khi chạy seeder này!');
            return;
        }

        // 5 công thức đã duyệt
        $approvedRecipes = [
            [
                'title' => 'Phở Gà Hà Nội',
                'description' => 'Phở gà truyền thống Hà Nội với nước dùng trong veo, thịt gà mềm và bánh phở dai.',
                'summary' => 'Phở gà Hà Nội - món ăn sáng truyền thống',
                'cooking_time' => 90,
                'preparation_time' => 25,
                'difficulty' => 'medium',
                'servings' => 4,
                'calories_per_serving' => 420,
                'ingredients' => [
                    ['name' => 'Gà ta', 'amount' => '1', 'unit' => 'con'],
                    ['name' => 'Bánh phở', 'amount' => '500g', 'unit' => 'g'],
                    ['name' => 'Gừng', 'amount' => '30g', 'unit' => 'g'],
                    ['name' => 'Hành tây', 'amount' => '2', 'unit' => 'củ'],
                    ['name' => 'Hành lá', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Rau thơm', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Nước mắm', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Chanh, ớt', 'amount' => 'vừa đủ', 'unit' => '']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Làm sạch gà, luộc với nước lạnh'],
                    ['step' => 2, 'instruction' => 'Nướng gừng và hành tây cho thơm'],
                    ['step' => 3, 'instruction' => 'Nấu nước dùng với gà, gừng, hành'],
                    ['step' => 4, 'instruction' => 'Thái thịt gà thành miếng vừa ăn'],
                    ['step' => 5, 'instruction' => 'Trụng bánh phở, xếp thịt gà'],
                    ['step' => 6, 'instruction' => 'Chan nước dùng, thêm rau thơm và thưởng thức']
                ],
                'tips' => 'Nước dùng phải trong veo, không đục. Thịt gà phải mềm nhưng không bở.',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approved_at' => now()->subDays(rand(1, 30)),
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(100, 2000),
                'favorite_count' => rand(10, 150),
                'rating_count' => rand(5, 80),
                'average_rating' => round(rand(35, 50) / 10, 1)
            ],
            [
                'title' => 'Bún Bò Huế',
                'description' => 'Bún bò Huế với nước dùng cay nồng, thịt bò mềm và bún sợi to.',
                'summary' => 'Bún bò Huế - món ăn đặc trưng xứ Huế',
                'cooking_time' => 150,
                'preparation_time' => 30,
                'difficulty' => 'hard',
                'servings' => 6,
                'calories_per_serving' => 480,
                'ingredients' => [
                    ['name' => 'Thịt bò', 'amount' => '500g', 'unit' => 'g'],
                    ['name' => 'Giò heo', 'amount' => '300g', 'unit' => 'g'],
                    ['name' => 'Bún sợi to', 'amount' => '600g', 'unit' => 'g'],
                    ['name' => 'Sả', 'amount' => '100g', 'unit' => 'g'],
                    ['name' => 'Mắm ruốc', 'amount' => '50g', 'unit' => 'g'],
                    ['name' => 'Ớt hiểm', 'amount' => '10', 'unit' => 'quả'],
                    ['name' => 'Rau sống', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Chanh, ớt', 'amount' => 'vừa đủ', 'unit' => '']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Nướng sả cho thơm'],
                    ['step' => 2, 'instruction' => 'Luộc thịt bò và giò heo'],
                    ['step' => 3, 'instruction' => 'Nấu nước dùng với sả, mắm ruốc'],
                    ['step' => 4, 'instruction' => 'Thái thịt bò và giò heo'],
                    ['step' => 5, 'instruction' => 'Trụng bún, xếp thịt'],
                    ['step' => 6, 'instruction' => 'Chan nước dùng cay, thêm rau và thưởng thức']
                ],
                'tips' => 'Nước dùng phải có vị cay nồng đặc trưng. Sả phải nướng thơm trước khi nấu.',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approved_at' => now()->subDays(rand(1, 30)),
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(100, 2000),
                'favorite_count' => rand(10, 150),
                'rating_count' => rand(5, 80),
                'average_rating' => round(rand(35, 50) / 10, 1)
            ],
            [
                'title' => 'Cơm Tấm Sườn Nướng',
                'description' => 'Cơm tấm với sườn nướng thơm lừng, chả trứng và nước mắm pha.',
                'summary' => 'Cơm tấm sườn nướng - món ăn sáng phổ biến',
                'cooking_time' => 70,
                'preparation_time' => 20,
                'difficulty' => 'medium',
                'servings' => 2,
                'calories_per_serving' => 550,
                'ingredients' => [
                    ['name' => 'Gạo tấm', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Sườn heo', 'amount' => '300g', 'unit' => 'g'],
                    ['name' => 'Chả trứng', 'amount' => '2', 'unit' => 'cái'],
                    ['name' => 'Nước mắm', 'amount' => '3', 'unit' => 'muỗng'],
                    ['name' => 'Đường', 'amount' => '2', 'unit' => 'muỗng'],
                    ['name' => 'Tỏi', 'amount' => '5', 'unit' => 'tép'],
                    ['name' => 'Dưa leo', 'amount' => '1', 'unit' => 'quả'],
                    ['name' => 'Cà chua', 'amount' => '2', 'unit' => 'quả']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Vo gạo tấm và nấu cơm'],
                    ['step' => 2, 'instruction' => 'Ướp sườn với nước mắm, đường, tỏi'],
                    ['step' => 3, 'instruction' => 'Nướng sườn trên than hoa'],
                    ['step' => 4, 'instruction' => 'Làm chả trứng'],
                    ['step' => 5, 'instruction' => 'Thái dưa leo, cà chua'],
                    ['step' => 6, 'instruction' => 'Bày cơm, sườn, chả và rau củ']
                ],
                'tips' => 'Gạo tấm phải nấu vừa chín tới. Sườn nướng phải có độ cháy xém và thơm.',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approved_at' => now()->subDays(rand(1, 30)),
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(100, 2000),
                'favorite_count' => rand(10, 150),
                'rating_count' => rand(5, 80),
                'average_rating' => round(rand(35, 50) / 10, 1)
            ],
            [
                'title' => 'Bánh Xèo Miền Tây',
                'description' => 'Bánh xèo miền Tây với bột giòn, nhân tôm thịt và rau sống.',
                'summary' => 'Bánh xèo miền Tây - món ăn đặc trưng vùng sông nước',
                'cooking_time' => 40,
                'preparation_time' => 25,
                'difficulty' => 'medium',
                'servings' => 4,
                'calories_per_serving' => 380,
                'ingredients' => [
                    ['name' => 'Bột bánh xèo', 'amount' => '300g', 'unit' => 'g'],
                    ['name' => 'Tôm', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Thịt ba chỉ', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Giá đỗ', 'amount' => '100g', 'unit' => 'g'],
                    ['name' => 'Hành lá', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Nước mắm', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Rau sống', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Dầu ăn', 'amount' => 'vừa đủ', 'unit' => '']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Pha bột bánh xèo với nước'],
                    ['step' => 2, 'instruction' => 'Thái thịt ba chỉ thành miếng nhỏ'],
                    ['step' => 3, 'instruction' => 'Đổ bột vào chảo nóng'],
                    ['step' => 4, 'instruction' => 'Xếp tôm, thịt, giá đỗ lên bánh'],
                    ['step' => 5, 'instruction' => 'Gập bánh lại và nướng giòn'],
                    ['step' => 6, 'instruction' => 'Bày rau sống và chấm nước mắm']
                ],
                'tips' => 'Bánh phải giòn bên ngoài, mềm bên trong. Nước mắm pha chua ngọt vừa ăn.',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approved_at' => now()->subDays(rand(1, 30)),
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(100, 2000),
                'favorite_count' => rand(10, 150),
                'rating_count' => rand(5, 80),
                'average_rating' => round(rand(35, 50) / 10, 1)
            ],
            [
                'title' => 'Chè Ba Màu',
                'description' => 'Chè ba màu truyền thống với đậu xanh, bột lọc và nước cốt dừa.',
                'summary' => 'Chè ba màu - món tráng miệng mát lạnh',
                'cooking_time' => 60,
                'preparation_time' => 30,
                'difficulty' => 'medium',
                'servings' => 6,
                'calories_per_serving' => 280,
                'ingredients' => [
                    ['name' => 'Đậu xanh', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Bột năng', 'amount' => '100g', 'unit' => 'g'],
                    ['name' => 'Nước cốt dừa', 'amount' => '400ml', 'unit' => 'ml'],
                    ['name' => 'Đường', 'amount' => '150g', 'unit' => 'g'],
                    ['name' => 'Lá dứa', 'amount' => '2', 'unit' => 'lá'],
                    ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'muỗng'],
                    ['name' => 'Đá viên', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Rau câu', 'amount' => '50g', 'unit' => 'g']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Nấu đậu xanh với đường và lá dứa'],
                    ['step' => 2, 'instruction' => 'Làm bột lọc với màu xanh'],
                    ['step' => 3, 'instruction' => 'Làm rau câu với màu đỏ'],
                    ['step' => 4, 'instruction' => 'Nấu nước cốt dừa với đường'],
                    ['step' => 5, 'instruction' => 'Xếp từng lớp vào ly'],
                    ['step' => 6, 'instruction' => 'Thêm đá và thưởng thức']
                ],
                'tips' => 'Đậu xanh phải nấu mềm nhưng không nát. Bột lọc phải dai và trong.',
                'status' => 'approved',
                'approved_by' => $users->random()->id,
                'approved_at' => now()->subDays(rand(1, 30)),
                'published_at' => now()->subDays(rand(1, 30)),
                'view_count' => rand(100, 2000),
                'favorite_count' => rand(10, 150),
                'rating_count' => rand(5, 80),
                'average_rating' => round(rand(35, 50) / 10, 1)
            ]
        ];

        // 5 công thức chưa duyệt
        $pendingRecipes = [
            [
                'title' => 'Bún Riêu Cua',
                'description' => 'Bún riêu cua với nước dùng đậm đà, riêu cua mềm và bún tươi.',
                'summary' => 'Bún riêu cua - món ăn dân dã miền Bắc',
                'cooking_time' => 120,
                'preparation_time' => 40,
                'difficulty' => 'hard',
                'servings' => 4,
                'calories_per_serving' => 450,
                'ingredients' => [
                    ['name' => 'Cua đồng', 'amount' => '1kg', 'unit' => 'kg'],
                    ['name' => 'Bún tươi', 'amount' => '400g', 'unit' => 'g'],
                    ['name' => 'Cà chua', 'amount' => '4', 'unit' => 'quả'],
                    ['name' => 'Hành tây', 'amount' => '2', 'unit' => 'củ'],
                    ['name' => 'Rau sống', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Nước mắm', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Chanh, ớt', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Hành lá', 'amount' => '1', 'unit' => 'bó']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Làm sạch cua đồng, giã lấy nước'],
                    ['step' => 2, 'instruction' => 'Lọc nước cua qua rây'],
                    ['step' => 3, 'instruction' => 'Nấu nước dùng với nước cua'],
                    ['step' => 4, 'instruction' => 'Thêm cà chua và hành tây'],
                    ['step' => 5, 'instruction' => 'Trụng bún, xếp rau sống'],
                    ['step' => 6, 'instruction' => 'Chan nước dùng và thưởng thức']
                ],
                'tips' => 'Nước cua phải lọc kỹ để không có vỏ. Riêu cua phải mềm và thơm.',
                'status' => 'pending',
                'published_at' => null,
                'view_count' => 0,
                'favorite_count' => 0,
                'rating_count' => 0,
                'average_rating' => 0.0
            ],
            [
                'title' => 'Bánh Cuốn Hà Nội',
                'description' => 'Bánh cuốn Hà Nội với bánh mỏng, nhân thịt xay và nước mắm pha.',
                'summary' => 'Bánh cuốn Hà Nội - món ăn sáng tinh tế',
                'cooking_time' => 45,
                'preparation_time' => 30,
                'difficulty' => 'hard',
                'servings' => 3,
                'calories_per_serving' => 320,
                'ingredients' => [
                    ['name' => 'Bột gạo', 'amount' => '300g', 'unit' => 'g'],
                    ['name' => 'Thịt heo xay', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Nấm mèo', 'amount' => '50g', 'unit' => 'g'],
                    ['name' => 'Hành khô', 'amount' => '3', 'unit' => 'củ'],
                    ['name' => 'Nước mắm', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Chanh', 'amount' => '1', 'unit' => 'quả'],
                    ['name' => 'Ớt', 'amount' => '2', 'unit' => 'quả'],
                    ['name' => 'Hành lá', 'amount' => '1', 'unit' => 'bó']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Pha bột gạo với nước'],
                    ['step' => 2, 'instruction' => 'Làm nhân thịt với nấm mèo'],
                    ['step' => 3, 'instruction' => 'Đổ bột lên vải căng'],
                    ['step' => 4, 'instruction' => 'Xếp nhân và cuốn bánh'],
                    ['step' => 5, 'instruction' => 'Cắt bánh thành miếng'],
                    ['step' => 6, 'instruction' => 'Bày ra đĩa và chấm nước mắm']
                ],
                'tips' => 'Bánh phải mỏng và dai. Nhân thịt phải thơm và không bị khô.',
                'status' => 'pending',
                'published_at' => null,
                'view_count' => 0,
                'favorite_count' => 0,
                'rating_count' => 0,
                'average_rating' => 0.0
            ],
            [
                'title' => 'Canh Chua Cá Lóc',
                'description' => 'Canh chua cá lóc với dứa, cà chua và rau răm.',
                'summary' => 'Canh chua cá lóc - món ăn đặc trưng miền Tây',
                'cooking_time' => 50,
                'preparation_time' => 20,
                'difficulty' => 'medium',
                'servings' => 4,
                'calories_per_serving' => 280,
                'ingredients' => [
                    ['name' => 'Cá lóc', 'amount' => '500g', 'unit' => 'g'],
                    ['name' => 'Dứa', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Cà chua', 'amount' => '3', 'unit' => 'quả'],
                    ['name' => 'Đậu bắp', 'amount' => '100g', 'unit' => 'g'],
                    ['name' => 'Rau răm', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Nước mắm', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Đường', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Ớt', 'amount' => '2', 'unit' => 'quả']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Làm sạch cá lóc, cắt khúc'],
                    ['step' => 2, 'instruction' => 'Nấu nước dùng với cá'],
                    ['step' => 3, 'instruction' => 'Thêm dứa và cà chua'],
                    ['step' => 4, 'instruction' => 'Nêm nước mắm và đường'],
                    ['step' => 5, 'instruction' => 'Thêm đậu bắp và rau răm'],
                    ['step' => 6, 'instruction' => 'Tắt bếp và thưởng thức']
                ],
                'tips' => 'Canh phải có vị chua ngọt hài hòa. Cá phải mềm nhưng không bị nát.',
                'status' => 'pending',
                'published_at' => null,
                'view_count' => 0,
                'favorite_count' => 0,
                'rating_count' => 0,
                'average_rating' => 0.0
            ],
            [
                'title' => 'Bánh Tét',
                'description' => 'Bánh tét truyền thống với nhân đậu xanh và thịt mỡ.',
                'summary' => 'Bánh tét - món ăn truyền thống ngày Tết',
                'cooking_time' => 180,
                'preparation_time' => 60,
                'difficulty' => 'hard',
                'servings' => 8,
                'calories_per_serving' => 400,
                'ingredients' => [
                    ['name' => 'Gạo nếp', 'amount' => '1kg', 'unit' => 'kg'],
                    ['name' => 'Đậu xanh', 'amount' => '300g', 'unit' => 'g'],
                    ['name' => 'Thịt mỡ', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Lá chuối', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Dây lạt', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Muối', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Nước mắm', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Tiêu', 'amount' => 'vừa đủ', 'unit' => '']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Ngâm gạo nếp và đậu xanh'],
                    ['step' => 2, 'instruction' => 'Nấu đậu xanh với muối'],
                    ['step' => 3, 'instruction' => 'Ướp thịt mỡ với nước mắm, tiêu'],
                    ['step' => 4, 'instruction' => 'Gói bánh với lá chuối'],
                    ['step' => 5, 'instruction' => 'Buộc chặt bằng dây lạt'],
                    ['step' => 6, 'instruction' => 'Luộc bánh trong 3-4 tiếng']
                ],
                'tips' => 'Bánh phải gói chặt tay. Thời gian luộc phải đủ để bánh chín đều.',
                'status' => 'pending',
                'published_at' => null,
                'view_count' => 0,
                'favorite_count' => 0,
                'rating_count' => 0,
                'average_rating' => 0.0
            ],
            [
                'title' => 'Chè Hạt Sen Long Nhãn',
                'description' => 'Chè hạt sen long nhãn với nước đường thơm và hạt sen mềm.',
                'summary' => 'Chè hạt sen long nhãn - món tráng miệng bổ dưỡng',
                'cooking_time' => 90,
                'preparation_time' => 30,
                'difficulty' => 'medium',
                'servings' => 6,
                'calories_per_serving' => 250,
                'ingredients' => [
                    ['name' => 'Hạt sen tươi', 'amount' => '300g', 'unit' => 'g'],
                    ['name' => 'Long nhãn', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Đường phèn', 'amount' => '150g', 'unit' => 'g'],
                    ['name' => 'Lá dứa', 'amount' => '2', 'unit' => 'lá'],
                    ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'muỗng'],
                    ['name' => 'Đá viên', 'amount' => 'vừa đủ', 'unit' => ''],
                    ['name' => 'Nước lọc', 'amount' => '1.5l', 'unit' => 'l'],
                    ['name' => 'Vani', 'amount' => '1', 'unit' => 'ống']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Làm sạch hạt sen, bỏ tim'],
                    ['step' => 2, 'instruction' => 'Nấu hạt sen với nước và muối'],
                    ['step' => 3, 'instruction' => 'Thêm đường phèn và lá dứa'],
                    ['step' => 4, 'instruction' => 'Nấu đến khi hạt sen mềm'],
                    ['step' => 5, 'instruction' => 'Thêm long nhãn và vani'],
                    ['step' => 6, 'instruction' => 'Để nguội và thưởng thức với đá']
                ],
                'tips' => 'Hạt sen phải mềm nhưng không bị nát. Nước đường phải trong và thơm.',
                'status' => 'pending',
                'published_at' => null,
                'view_count' => 0,
                'favorite_count' => 0,
                'rating_count' => 0,
                'average_rating' => 0.0
            ]
        ];

        // Tạo công thức đã duyệt
        foreach ($approvedRecipes as $recipeData) {
            $recipe = Recipe::create([
                'user_id' => $users->random()->id,
                'title' => $recipeData['title'],
                'slug' => (new \App\Models\Recipe())->generateUniqueSlug($recipeData['title']),
                'description' => $recipeData['description'],
                'summary' => $recipeData['summary'],
                'cooking_time' => $recipeData['cooking_time'],
                'preparation_time' => $recipeData['preparation_time'],
                'difficulty' => $recipeData['difficulty'],
                'servings' => $recipeData['servings'],
                'calories_per_serving' => $recipeData['calories_per_serving'],
                'ingredients' => $recipeData['ingredients'],
                'instructions' => $recipeData['instructions'],
                'tips' => $recipeData['tips'],
                'status' => $recipeData['status'],
                'approved_by' => $recipeData['approved_by'],
                'approved_at' => $recipeData['approved_at'],
                'published_at' => $recipeData['published_at'],
                'view_count' => $recipeData['view_count'],
                'favorite_count' => $recipeData['favorite_count'],
                'rating_count' => $recipeData['rating_count'],
                'average_rating' => $recipeData['average_rating']
            ]);

            // Gán categories ngẫu nhiên
            $recipe->categories()->attach($categories->random(rand(1, 2))->pluck('id'));

            // Gán tags ngẫu nhiên
            $recipe->tags()->attach($tags->random(rand(2, 4))->pluck('id'));
        }

        // Tạo công thức chưa duyệt
        foreach ($pendingRecipes as $recipeData) {
            $recipe = Recipe::create([
                'user_id' => $users->random()->id,
                'title' => $recipeData['title'],
                'slug' => (new \App\Models\Recipe())->generateUniqueSlug($recipeData['title']),
                'description' => $recipeData['description'],
                'summary' => $recipeData['summary'],
                'cooking_time' => $recipeData['cooking_time'],
                'preparation_time' => $recipeData['preparation_time'],
                'difficulty' => $recipeData['difficulty'],
                'servings' => $recipeData['servings'],
                'calories_per_serving' => $recipeData['calories_per_serving'],
                'ingredients' => $recipeData['ingredients'],
                'instructions' => $recipeData['instructions'],
                'tips' => $recipeData['tips'],
                'status' => $recipeData['status'],
                'published_at' => $recipeData['published_at'],
                'view_count' => $recipeData['view_count'],
                'favorite_count' => $recipeData['favorite_count'],
                'rating_count' => $recipeData['rating_count'],
                'average_rating' => $recipeData['average_rating']
            ]);

            // Gán categories ngẫu nhiên
            $recipe->categories()->attach($categories->random(rand(1, 2))->pluck('id'));

            // Gán tags ngẫu nhiên
            $recipe->tags()->attach($tags->random(rand(2, 4))->pluck('id'));
        }

        $this->command->info('Đã tạo thành công 10 công thức nấu ăn:');
        $this->command->info('- 5 công thức đã duyệt');
        $this->command->info('- 5 công thức chưa duyệt');
    }
} 