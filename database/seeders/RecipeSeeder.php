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

        // Đọc dữ liệu từ file JSON
        $jsonPath = storage_path('app/recipes_data.json');
        if (!file_exists($jsonPath)) {
            $this->command->error('File recipes_data.json không tồn tại!');
            return;
        }

        $jsonData = file_get_contents($jsonPath);
        $recipes = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Lỗi khi đọc file JSON: ' . json_last_error_msg());
            return;
        }

        $this->command->info('Đang tạo ' . count($recipes) . ' món ăn từ file JSON...');

        foreach ($recipes as $index => $recipeData) {
            $recipe = Recipe::create([
                'user_id' => $users->random()->id,
                'title' => $recipeData['title'],
                'slug' => Str::slug($recipeData['title']) . '-' . time() . '-' . $index,
                'description' => $recipeData['description'],
                'cooking_time' => $recipeData['cooking_time'],
                'difficulty' => $recipeData['difficulty'],
                'servings' => $recipeData['servings'],
                'ingredients' => $recipeData['ingredients'],
                'instructions' => $recipeData['instructions'],
                'status' => 'approved',
                'published_at' => now()->subDays(rand(1, 120)),
                'view_count' => 0,
                'favorite_count' => 0,
                'rating_count' => 0,
                'average_rating' => 0
            ]);

            // Gán categories ngẫu nhiên
            $recipe->categories()->attach($categories->random(rand(1, 2))->pluck('id'));

            // Gán tags ngẫu nhiên
            $recipe->tags()->attach($tags->random(rand(2, 4))->pluck('id'));
        }

        $this->command->info('Đã tạo thành công ' . count($recipes) . ' món ăn từ file JSON!');
    }
}