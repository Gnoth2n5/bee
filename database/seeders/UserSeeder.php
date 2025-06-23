<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin BeeFood',
            'email' => 'admin@beefood.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'avatar' => null,
            'bio' => 'Quản trị viên hệ thống BeeFood',
            'preferences' => json_encode([
                'theme' => 'light',
                'notifications' => true,
                'language' => 'vi'
            ]),
            'status' => 'active'
        ]);

        // Create admin profile
        UserProfile::create([
            'user_id' => $admin->id,
            'phone' => '0123456789',
            'address' => 'Hà Nội, Việt Nam',
            'city' => 'Hà Nội',
            'country' => 'Vietnam',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'language' => 'vi',
            'dietary_preferences' => json_encode(['none']),
            'allergies' => json_encode([]),
            'health_conditions' => json_encode([]),
            'cooking_experience' => 'advanced'
        ]);

        // Create manager user
        $manager = User::create([
            'name' => 'Manager BeeFood',
            'email' => 'manager@beefood.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'avatar' => null,
            'bio' => 'Quản lý nội dung BeeFood',
            'preferences' => json_encode([
                'theme' => 'light',
                'notifications' => true,
                'language' => 'vi'
            ]),
            'status' => 'active'
        ]);

        // Create manager profile
        UserProfile::create([
            'user_id' => $manager->id,
            'phone' => '0987654321',
            'address' => 'TP.HCM, Việt Nam',
            'city' => 'TP.HCM',
            'country' => 'Vietnam',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'language' => 'vi',
            'dietary_preferences' => json_encode(['vegetarian']),
            'allergies' => json_encode(['nuts']),
            'health_conditions' => json_encode([]),
            'cooking_experience' => 'intermediate'
        ]);

        // Create sample users
        $users = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'user1@beefood.com',
                'bio' => 'Yêu thích nấu ăn truyền thống Việt Nam',
                'dietary_preferences' => ['none'],
                'allergies' => [],
                'cooking_experience' => 'beginner'
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'user2@beefood.com',
                'bio' => 'Chuyên gia ẩm thực châu Á',
                'dietary_preferences' => ['vegan'],
                'allergies' => ['dairy'],
                'cooking_experience' => 'advanced'
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'user3@beefood.com',
                'bio' => 'Đầu bếp nghiệp dư, thích thử nghiệm món mới',
                'dietary_preferences' => ['gluten-free'],
                'allergies' => ['shellfish'],
                'cooking_experience' => 'intermediate'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'avatar' => null,
                'bio' => $userData['bio'],
                'preferences' => json_encode([
                    'theme' => 'light',
                    'notifications' => true,
                    'language' => 'vi'
                ]),
                'status' => 'active'
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'phone' => null,
                'address' => null,
                'city' => 'Hà Nội',
                'country' => 'Vietnam',
                'timezone' => 'Asia/Ho_Chi_Minh',
                'language' => 'vi',
                'dietary_preferences' => json_encode($userData['dietary_preferences']),
                'allergies' => json_encode($userData['allergies']),
                'health_conditions' => json_encode([]),
                'cooking_experience' => $userData['cooking_experience']
            ]);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin: admin@beefood.com / password');
        $this->command->info('Manager: manager@beefood.com / password');
        $this->command->info('Users: user1@beefood.com, user2@beefood.com, user3@beefood.com / password');
    }
}