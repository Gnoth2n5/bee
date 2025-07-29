<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chạy các seeders theo thứ tự
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            RolePermissionSeeder::class,
            ModerationRuleSeeder::class,
            RecipeSeeder::class,
            PostSeeder::class,
        ]);

        // Tạo thêm user test nếu cần
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
