<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(5, true),
            'excerpt' => fake()->paragraph(2),
            'featured_image' => null,
            'status' => fake()->randomElement(['draft', 'pending', 'published', 'archived']),
            'published_at' => null,
            'user_id' => User::factory(),
            'view_count' => fake()->numberBetween(0, 1000),
            'meta_title' => fake()->sentence(3),
            'meta_description' => fake()->paragraph(1),
            'meta_keywords' => fake()->words(5, true),
        ];
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the post is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the post is archived.
     */
    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'archived',
            'published_at' => fake()->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }

    /**
     * Indicate that the post has a featured image.
     */
    public function withFeaturedImage(): static
    {
        return $this->state(fn(array $attributes) => [
            'featured_image' => 'posts/featured-' . fake()->uuid() . '.jpg',
        ]);
    }
}
