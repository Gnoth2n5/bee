<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
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
            return;
        }

        $recipes = [
            [
                'title' => 'Phở Bò Việt Nam',
                'description' => 'Món phở bò truyền thống Việt Nam với nước dùng đậm đà, bánh phở mềm và thịt bò tươi ngon.',
                'summary' => 'Phở bò Việt Nam - món ăn quốc hồn quốc túy',
                'cooking_time' => 120,
                'preparation_time' => 30,
                'difficulty' => 'medium',
                'servings' => 4,
                'calories_per_serving' => 450,
                'ingredients' => [
                    ['name' => 'Bánh phở', 'amount' => '500g', 'unit' => 'g'],
                    ['name' => 'Thịt bò', 'amount' => '400g', 'unit' => 'g'],
                    ['name' => 'Xương bò', 'amount' => '1kg', 'unit' => 'kg'],
                    ['name' => 'Gừng', 'amount' => '50g', 'unit' => 'g'],
                    ['name' => 'Hành tây', 'amount' => '2', 'unit' => 'củ'],
                    ['name' => 'Gia vị phở', 'amount' => '1', 'unit' => 'gói'],
                    ['name' => 'Rau thơm', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Chanh, ớt', 'amount' => 'vừa đủ', 'unit' => '']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Nướng gừng và hành tây cho thơm'],
                    ['step' => 2, 'instruction' => 'Luộc xương bò với nước lạnh, đổ bỏ nước đầu'],
                    ['step' => 3, 'instruction' => 'Nấu nước dùng với xương, gừng, hành và gia vị'],
                    ['step' => 4, 'instruction' => 'Thái thịt bò mỏng, trần qua nước sôi'],
                    ['step' => 5, 'instruction' => 'Trụng bánh phở, xếp thịt bò, chan nước dùng'],
                    ['step' => 6, 'instruction' => 'Thêm rau thơm, chanh, ớt và thưởng thức']
                ],
                'tips' => 'Nước dùng phải nấu ít nhất 3-4 tiếng để có vị đậm đà. Thịt bò nên thái mỏng và trần qua nước sôi để giữ độ tươi.',
                'status' => 'approved',
                'published_at' => now(),
                'view_count' => 1250,
                'favorite_count' => 89,
                'rating_count' => 45,
                'average_rating' => 4.8
            ],
            [
                'title' => 'Bún Chả Hà Nội',
                'description' => 'Bún chả Hà Nội với thịt nướng thơm lừng, nước mắm pha chua ngọt và bún tươi.',
                'summary' => 'Bún chả Hà Nội - món ăn đặc trưng của thủ đô',
                'cooking_time' => 45,
                'preparation_time' => 20,
                'difficulty' => 'easy',
                'servings' => 2,
                'calories_per_serving' => 380,
                'ingredients' => [
                    ['name' => 'Thịt ba chỉ', 'amount' => '300g', 'unit' => 'g'],
                    ['name' => 'Bún tươi', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Nước mắm', 'amount' => '3', 'unit' => 'muỗng'],
                    ['name' => 'Đường', 'amount' => '2', 'unit' => 'muỗng'],
                    ['name' => 'Chanh', 'amount' => '1', 'unit' => 'quả'],
                    ['name' => 'Tỏi', 'amount' => '3', 'unit' => 'tép'],
                    ['name' => 'Ớt', 'amount' => '2', 'unit' => 'quả'],
                    ['name' => 'Rau sống', 'amount' => '1', 'unit' => 'bó']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Thái thịt ba chỉ thành miếng vừa ăn'],
                    ['step' => 2, 'instruction' => 'Ướp thịt với nước mắm, đường, tỏi băm'],
                    ['step' => 3, 'instruction' => 'Nướng thịt trên than hoa hoặc bếp gas'],
                    ['step' => 4, 'instruction' => 'Pha nước mắm chua ngọt'],
                    ['step' => 5, 'instruction' => 'Trụng bún, xếp rau sống'],
                    ['step' => 6, 'instruction' => 'Bày thịt nướng, chan nước mắm và thưởng thức']
                ],
                'tips' => 'Thịt nướng phải có độ cháy xém bên ngoài, bên trong vẫn mềm. Nước mắm pha theo tỷ lệ 1:1:1 (nước mắm:đường:chanh).',
                'status' => 'approved',
                'published_at' => now()->subDays(2),
                'view_count' => 890,
                'favorite_count' => 67,
                'rating_count' => 32,
                'average_rating' => 4.6
            ],
            [
                'title' => 'Cơm Tấm Sài Gòn',
                'description' => 'Cơm tấm Sài Gòn với sườn nướng, chả trứng và nước mắm pha đặc biệt.',
                'summary' => 'Cơm tấm Sài Gòn - món ăn sáng phổ biến miền Nam',
                'cooking_time' => 60,
                'preparation_time' => 25,
                'difficulty' => 'medium',
                'servings' => 2,
                'calories_per_serving' => 520,
                'ingredients' => [
                    ['name' => 'Gạo tấm', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Sườn heo', 'amount' => '300g', 'unit' => 'g'],
                    ['name' => 'Chả trứng', 'amount' => '2', 'unit' => 'cái'],
                    ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'muỗng'],
                    ['name' => 'Đường', 'amount' => '1', 'unit' => 'muỗng'],
                    ['name' => 'Tỏi', 'amount' => '4', 'unit' => 'tép'],
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
                'tips' => 'Gạo tấm phải nấu vừa chín tới, không được nhão. Sườn nướng phải có độ cháy xém và thơm.',
                'status' => 'approved',
                'published_at' => now()->subDays(5),
                'view_count' => 756,
                'favorite_count' => 54,
                'rating_count' => 28,
                'average_rating' => 4.4
            ],
            [
                'title' => 'Bánh Mì Thịt Nướng',
                'description' => 'Bánh mì Việt Nam với thịt nướng, rau sống và nước sốt đặc biệt.',
                'summary' => 'Bánh mì thịt nướng - món ăn đường phố nổi tiếng',
                'cooking_time' => 30,
                'preparation_time' => 15,
                'difficulty' => 'easy',
                'servings' => 2,
                'calories_per_serving' => 320,
                'ingredients' => [
                    ['name' => 'Bánh mì', 'amount' => '2', 'unit' => 'ổ'],
                    ['name' => 'Thịt heo xay', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'muỗng'],
                    ['name' => 'Đường', 'amount' => '1', 'unit' => 'muỗng'],
                    ['name' => 'Tỏi', 'amount' => '3', 'unit' => 'tép'],
                    ['name' => 'Rau sống', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Dưa leo', 'amount' => '1', 'unit' => 'quả'],
                    ['name' => 'Ớt', 'amount' => '2', 'unit' => 'quả']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Ướp thịt với nước mắm, đường, tỏi'],
                    ['step' => 2, 'instruction' => 'Nướng thịt trên vỉ'],
                    ['step' => 3, 'instruction' => 'Nướng bánh mì cho giòn'],
                    ['step' => 4, 'instruction' => 'Thái rau sống, dưa leo'],
                    ['step' => 5, 'instruction' => 'Xếp thịt vào bánh mì'],
                    ['step' => 6, 'instruction' => 'Thêm rau sống và thưởng thức']
                ],
                'tips' => 'Bánh mì phải nướng giòn bên ngoài, mềm bên trong. Thịt nướng phải có độ cháy xém và thơm.',
                'status' => 'approved',
                'published_at' => now()->subDays(7),
                'view_count' => 634,
                'favorite_count' => 43,
                'rating_count' => 21,
                'average_rating' => 4.3
            ],
            [
                'title' => 'Gỏi Cuốn Tôm Thịt',
                'description' => 'Gỏi cuốn tươi với tôm, thịt luộc, rau sống và nước mắm pha.',
                'summary' => 'Gỏi cuốn tôm thịt - món ăn thanh mát, bổ dưỡng',
                'cooking_time' => 25,
                'preparation_time' => 20,
                'difficulty' => 'easy',
                'servings' => 4,
                'calories_per_serving' => 180,
                'ingredients' => [
                    ['name' => 'Bánh tráng', 'amount' => '20', 'unit' => 'lá'],
                    ['name' => 'Tôm', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Thịt ba chỉ', 'amount' => '200g', 'unit' => 'g'],
                    ['name' => 'Bún tươi', 'amount' => '100g', 'unit' => 'g'],
                    ['name' => 'Rau sống', 'amount' => '1', 'unit' => 'bó'],
                    ['name' => 'Nước mắm', 'amount' => '3', 'unit' => 'muỗng'],
                    ['name' => 'Đường', 'amount' => '2', 'unit' => 'muỗng'],
                    ['name' => 'Chanh', 'amount' => '1', 'unit' => 'quả']
                ],
                'instructions' => [
                    ['step' => 1, 'instruction' => 'Luộc tôm và thịt ba chỉ'],
                    ['step' => 2, 'instruction' => 'Thái tôm và thịt thành miếng nhỏ'],
                    ['step' => 3, 'instruction' => 'Trụng bún và để ráo'],
                    ['step' => 4, 'instruction' => 'Pha nước mắm chua ngọt'],
                    ['step' => 5, 'instruction' => 'Cuốn bánh tráng với tôm, thịt, bún, rau'],
                    ['step' => 6, 'instruction' => 'Chấm nước mắm và thưởng thức']
                ],
                'tips' => 'Bánh tráng phải nhúng nước vừa đủ, không được ướt quá. Cuốn chặt tay để không bị bung.',
                'status' => 'approved',
                'published_at' => now()->subDays(10),
                'view_count' => 445,
                'favorite_count' => 38,
                'rating_count' => 19,
                'average_rating' => 4.5
            ]
        ];

        foreach ($recipes as $recipeData) {
            $recipe = Recipe::create([
                'user_id' => $users->random()->id,
                'title' => $recipeData['title'],
                'slug' => Str::slug($recipeData['title']),
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
    }
} 