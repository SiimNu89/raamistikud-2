<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->latest()
            ->paginate(30)
            ->through(fn (Post $post) => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'created_at_formated' => $post->created_at_formatted, // uses trait
                'updated_at_formated' => $post->updated_at_formatted,
            ]);

        return Inertia::render('posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create()
    {
        return Inertia::render('posts/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ]);

        Post::create([
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $post->loadMissing(['comments.user']);

        return Inertia::render('posts/Show', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'created_at_formated' => $post->created_at_formatted,
                'updated_at_formated' => $post->updated_at_formatted,
                'comments' => $post->comments->map(fn($comment) => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => $comment->user ? [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                    ] : null,
                ])->toArray(),
            ]
        ]);
    }

    public function edit(Post $post)
    {
        return Inertia::render('posts/Edit', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'created_at' => $post->created_at?->toDateTimeString(),
                'updated_at' => $post->updated_at?->toDateTimeString(),
            ],
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ]);

        $post->update([
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
}
