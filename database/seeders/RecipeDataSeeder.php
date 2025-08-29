<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;

class RecipeDataSeeder extends Seeder
{
    public function run(): void
    {
        // Đọc dữ liệu từ file JSON
        $jsonFile = storage_path('app/recipes_data.json');
        if (!file_exists($jsonFile)) {
            $this->command->error('File recipes_data.json không tồn tại!');
            return;
        }

        $recipesData = json_decode(file_get_contents($jsonFile), true);
        if (!$recipesData) {
            $this->command->error('Không thể đọc dữ liệu JSON!');
            return;
        }

        // Lấy user đầu tiên để làm author
        $user = User::first();
        if (!$user) {
            $this->command->error('Không có user nào trong database!');
            return;
        }

        // Lấy tất cả categories và tags để mapping
        $categories = Category::all()->keyBy('name');
        $tags = Tag::all()->keyBy('name');

        $this->command->info('Bắt đầu import ' . count($recipesData) . ' món ăn...');

        foreach ($recipesData as $recipeData) {
            try {
                // Kiểm tra xem recipe đã tồn tại chưa
                $existingRecipe = Recipe::where('title', $recipeData['title'])->first();
                if ($existingRecipe) {
                    $this->command->info("⏭️ Bỏ qua: {$recipeData['title']} (đã tồn tại)");
                    continue;
                }

                // Tạo recipe
                $recipe = Recipe::create([
                    'title' => $recipeData['title'],
                    'slug' => $recipeData['slug'] ?? (new \App\Models\Recipe())->generateUniqueSlug($recipeData['title']),
                    'description' => $recipeData['description'] ?? '',
                    'image_url' => $recipeData['image_url'] ?? null,
                    'cooking_time' => $recipeData['cooking_time'] ?? rand(15, 120),
                    'difficulty' => $recipeData['difficulty'] ?? ['easy', 'medium', 'hard'][rand(0, 2)],
                    'servings' => $recipeData['servings'] ?? rand(2, 8),
                    'ingredients' => json_encode($recipeData['ingredients'] ?? []),
                    'instructions' => json_encode($recipeData['instructions'] ?? []),
                    'is_published' => $recipeData['is_published'] ?? true,
                    'user_id' => $user->id,
                ]);

                // Attach categories
                if (isset($recipeData['categories']) && is_array($recipeData['categories'])) {
                    $categoryIds = [];
                    foreach ($recipeData['categories'] as $categoryName) {
                        if ($categories->has($categoryName)) {
                            $categoryIds[] = $categories[$categoryName]->id;
                        }
                    }
                    if (!empty($categoryIds)) {
                        $recipe->categories()->attach($categoryIds);
                    }
                }

                // Attach tags
                if (isset($recipeData['tags']) && is_array($recipeData['tags'])) {
                    $tagIds = [];
                    foreach ($recipeData['tags'] as $tagName) {
                        if ($tags->has($tagName)) {
                            $tagIds[] = $tags[$tagName]->id;
                        }
                    }
                    if (!empty($tagIds)) {
                        $recipe->tags()->attach($tagIds);
                    }
                }

                $this->command->info("✅ Đã import: {$recipe->title}");

            } catch (\Exception $e) {
                $this->command->error("✗ Lỗi khi import {$recipeData['title']}: " . $e->getMessage());
            }
        }

        $this->command->info('✅ Hoàn thành import dữ liệu recipes!');
    }
}
