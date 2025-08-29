<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;

class RecipeTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo user mẫu nếu chưa có
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Tạo category mẫu
        $category = Category::firstOrCreate(
            ['name' => 'Món chính'],
            [
                'slug' => 'mon-chinh',
                'description' => 'Các món ăn chính',
            ]
        );

        // Tạo tag mẫu
        $tag = Tag::firstOrCreate(
            ['name' => 'Ngon'],
            [
                'slug' => 'ngon',
                'description' => 'Món ăn ngon',
            ]
        );

        // Tạo recipes mẫu
        $recipes = [
            [
                'title' => 'Cơm gà xối mỡ',
                'description' => 'Món cơm gà truyền thống Việt Nam với gà xối mỡ thơm ngon',
                'cooking_time' => 30,
                'difficulty' => 'medium',
                'status' => 'approved',
            ],
            [
                'title' => 'Phở bò',
                'description' => 'Phở bò truyền thống với nước dùng đậm đà',
                'cooking_time' => 60,
                'difficulty' => 'hard',
                'status' => 'approved',
            ],
            [
                'title' => 'Bún chả',
                'description' => 'Bún chả Hà Nội với thịt nướng thơm ngon',
                'cooking_time' => 45,
                'difficulty' => 'medium',
                'status' => 'approved',
            ],
            [
                'title' => 'Bánh mì thịt',
                'description' => 'Bánh mì thịt nướng với rau sống và nước mắm',
                'cooking_time' => 15,
                'difficulty' => 'easy',
                'status' => 'approved',
            ],
            [
                'title' => 'Canh chua cá',
                'description' => 'Canh chua cá lóc với dứa và cà chua',
                'cooking_time' => 40,
                'difficulty' => 'medium',
                'status' => 'approved',
            ],
        ];

        foreach ($recipes as $recipeData) {
            $recipe = Recipe::firstOrCreate(
                ['title' => $recipeData['title']],
                array_merge($recipeData, [
                    'user_id' => $user->id,
                    'slug' => (new \App\Models\Recipe())->generateUniqueSlug($recipeData['title']),
                    'ingredients' => json_encode(['Thịt', 'Gia vị', 'Rau']),
                    'instructions' => json_encode(['Bước 1: Chuẩn bị nguyên liệu', 'Bước 2: Nấu ăn', 'Bước 3: Thưởng thức']),
                    'servings' => 4,
                    'calories_per_serving' => 300,
                ])
            );

            // Gán category và tag
            $recipe->categories()->sync([$category->id]);
            $recipe->tags()->sync([$tag->id]);
        }

        $this->command->info('Đã tạo ' . count($recipes) . ' recipes mẫu!');
    }
}
