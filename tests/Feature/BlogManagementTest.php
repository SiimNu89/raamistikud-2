<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can create blog posts', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'My first blog post',
            'description' => 'This is the body of the blog post.',
        ]);

    $response->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'title' => 'My first blog post',
        'description' => 'This is the body of the blog post.',
    ]);
});

test('authenticated users can update blog posts', function () {
    $user = User::factory()->create();

    $post = $this
        ->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Draft',
            'description' => 'Old description',
        ]);

    $blogPost = Post::query()->latest('id')->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->put(route('posts.update', $blogPost), [
            'title' => 'Updated title',
            'description' => 'Updated description',
        ]);

    $response->assertRedirect(route('posts.index'));

    $this->assertDatabaseHas('posts', [
        'id' => $blogPost->id,
        'title' => 'Updated title',
        'description' => 'Updated description',
    ]);
});

test('authenticated users can add comments to blog posts', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)->post(route('posts.store'), [
        'title' => 'Post with comments',
        'description' => 'Blog description',
    ]);

    $post = Post::query()->latest('id')->firstOrFail();
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('comments.add', $post), [
            'content' => 'Nice post!',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'content' => 'Nice post!',
    ]);
});

test('admins can delete comments', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $this->actingAs($admin)->post(route('posts.store'), [
        'title' => 'Post',
        'description' => 'Description',
    ]);

    $post = Post::query()->latest('id')->firstOrFail();
    $user = User::factory()->create();

    $comment = Comment::create([
        'user_id' => $user->id,
        'post_id' => $post->id,
        'content' => 'Delete me',
    ]);

    $response = $this
        ->actingAs($admin)
        ->delete(route('comments.destroy', $comment));

    $response->assertRedirect();

    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});
