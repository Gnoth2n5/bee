<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@beefood.com')->first();

        // Main categories
        $mainCategories = [
            [
                'name' => 'Món Chính',
                'slug' => 'mon-chinh',
                'description' => 'Các món ăn chính trong bữa ăn',
                'icon' => 'utensils',
                'color' => '#FF6B6B',
                'sort_order' => 1
            ],
            [
                'name' => 'Món Khai Vị',
                'slug' => 'mon-khai-vi',
                'description' => 'Các món ăn khai vị, salad',
                'icon' => 'leaf',
                'color' => '#4ECDC4',
                'sort_order' => 2
            ],
            [
                'name' => 'Món Tráng Miệng',
                'slug' => 'mon-trang-mieng',
                'description' => 'Bánh, kem, chè và các món ngọt',
                'icon' => 'birthday-cake',
                'color' => '#FFE66D',
                'sort_order' => 3
            ],
            [
                'name' => 'Món Canh',
                'slug' => 'mon-canh',
                'description' => 'Các loại canh, súp',
                'icon' => 'bowl-rice',
                'color' => '#95E1D3',
                'sort_order' => 4
            ],
            [
                'name' => 'Món Nướng',
                'slug' => 'mon-nuong',
                'description' => 'Các món nướng, BBQ',
                'icon' => 'fire',
                'color' => '#FF8A80',
                'sort_order' => 5
            ],
            [
                'name' => 'Món Chiên',
                'slug' => 'mon-chien',
                'description' => 'Các món chiên, xào',
                'icon' => 'frying-pan',
                'color' => '#FFB74D',
                'sort_order' => 6
            ]
        ];

        foreach ($mainCategories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'description' => $categoryData['description'],
                'icon' => $categoryData['icon'],
                'color' => $categoryData['color'],
                'parent_id' => null,
                'level' => 0,
                'sort_order' => $categoryData['sort_order'],
                'is_active' => true,
                'created_by' => $admin->id
            ]);
        }

        // Sub-categories for Món Chính
        $monChinh = Category::where('slug', 'mon-chinh')->first();
        $subCategories = [
            [
                'name' => 'Cơm',
                'slug' => 'com',
                'description' => 'Các món cơm',
                'icon' => 'rice',
                'color' => '#FF6B6B',
                'parent_id' => $monChinh->id,
                'level' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'Phở',
                'slug' => 'pho',
                'description' => 'Các loại phở',
                'icon' => 'bowl-food',
                'color' => '#FF6B6B',
                'parent_id' => $monChinh->id,
                'level' => 1,
                'sort_order' => 2
            ],
            [
                'name' => 'Bún',
                'slug' => 'bun',
                'description' => 'Các món bún',
                'icon' => 'noodles',
                'color' => '#FF6B6B',
                'parent_id' => $monChinh->id,
                'level' => 1,
                'sort_order' => 3
            ],
            [
                'name' => 'Mì',
                'slug' => 'mi',
                'description' => 'Các loại mì',
                'icon' => 'pasta',
                'color' => '#FF6B6B',
                'parent_id' => $monChinh->id,
                'level' => 1,
                'sort_order' => 4
            ]
        ];

        foreach ($subCategories as $subCategoryData) {
            Category::create([
                'name' => $subCategoryData['name'],
                'slug' => $subCategoryData['slug'],
                'description' => $subCategoryData['description'],
                'icon' => $subCategoryData['icon'],
                'color' => $subCategoryData['color'],
                'parent_id' => $subCategoryData['parent_id'],
                'level' => $subCategoryData['level'],
                'sort_order' => $subCategoryData['sort_order'],
                'is_active' => true,
                'created_by' => $admin->id
            ]);
        }

        // Sub-categories for Món Khai Vị
        $monKhaiVi = Category::where('slug', 'mon-khai-vi')->first();
        $khaiViCategories = [
            [
                'name' => 'Salad',
                'slug' => 'salad',
                'description' => 'Các loại salad',
                'icon' => 'salad',
                'color' => '#4ECDC4',
                'parent_id' => $monKhaiVi->id,
                'level' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'Gỏi',
                'slug' => 'goi',
                'description' => 'Các món gỏi',
                'icon' => 'leaf',
                'color' => '#4ECDC4',
                'parent_id' => $monKhaiVi->id,
                'level' => 1,
                'sort_order' => 2
            ]
        ];

        foreach ($khaiViCategories as $categoryData) {
            Category::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'description' => $categoryData['description'],
                'icon' => $categoryData['icon'],
                'color' => $categoryData['color'],
                'parent_id' => $categoryData['parent_id'],
                'level' => $categoryData['level'],
                'sort_order' => $categoryData['sort_order'],
                'is_active' => true,
                'created_by' => $admin->id
            ]);
        }

        $this->command->info('Categories seeded successfully!');
    }
}
