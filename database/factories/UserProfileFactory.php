<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => 'Vietnam',
            'dietary_preferences' => [],
            'allergies' => [],
            'health_conditions' => [],
            'cooking_experience' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'timezone' => 'Asia/Ho_Chi_Minh',
            'language' => 'vi',
        ];
    }
}
