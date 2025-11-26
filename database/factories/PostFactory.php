<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\Author;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'author_id' => Author::factory(),
            'published' => fake()->boolean(),
            'content' => fake()->paragraphs(3, true),
        ];
    }
}