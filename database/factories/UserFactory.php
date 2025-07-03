<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'avatar' => null,
            'bio' => fake()->sentence(),
            'preferences' => [],
            'status' => 'active',
            'last_login_at' => now(),
            'login_count' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function ($user) {
            // Create user profile
            $user->profile()->create([
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
            ]);
        });
    }
}
