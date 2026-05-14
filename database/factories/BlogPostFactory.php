<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);
        return [
            'title'           => $title,
            'slug'            => Str::slug($title) . '-' . Str::random(6),
            'excerpt'         => fake()->paragraph(),
            'body'            => fake()->paragraphs(5, true),
            'author_user_id'  => User::factory()->state(['role' => 'super_admin', 'is_active' => true]),
            'meta_title'      => $title,
            'meta_description'=> fake()->sentence(),
            'status'          => 'published',
            'published_at'    => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'published_at' => null]);
    }

    public function scheduledFuture(): static
    {
        return $this->state(fn () => ['status' => 'published', 'published_at' => now()->addDay()]);
    }
}
