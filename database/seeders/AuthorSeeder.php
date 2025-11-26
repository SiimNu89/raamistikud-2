<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;
use App\Models\Comment;
use App\Models\Post;
use Database\Factories\PostFactory;


class AuthorSeeder extends Seeder
{
    public function run(): void
    {


        $PostFactory = Post::factory(5)
->has(Comment::factory(3)->state(function (array $attributes, Post $post) {
            return ['user_id' => $post->author->user_id ?? 1];
        }), 'comments');

        Author::factory(100)
                    ->has($PostFactory, 'posts')
            ->create();
    }
}
