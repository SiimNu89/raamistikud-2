<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Author;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    // List paginated posts with authors
    public function index()
    {
        $posts = Post::with('author:id,first_name,last_name')
            ->paginate(30)
            ->through(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'author' => $post->author,
                'published' => $post->published,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'created_at_formatted' => $post->created_at?->format('Y-m-d H:i'),
                'updated_at_formatted' => $post->updated_at?->format('Y-m-d H:i'),
            ]);

        return Inertia::render('posts/Index', [
            'posts' => $posts,
        ]);
    }

    // Show create post form
    public function create()
    {
        $authors = Author::all()
            ->mapWithKeys(fn($author) => [$author->id => $author->first_name . ' ' . $author->last_name]);

        return Inertia::render('posts/Create', [
            'authors' => $authors,
        ]);
    }

    // Store new post
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_id' => 'required|integer|exists:authors,id',
            'published' => 'boolean',
        ]);

        Post::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'author_id' => $data['author_id'],
            'published' => $data['published'] ?? false,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    // Show single post
    public function show(Post $post)
    {
        return Inertia::render('posts/Show', [
            'post' => $post->loadMissing('author'),
        ]);
    }

    // Edit post form
    public function edit(Post $post)
    {
        $authors = Author::all()
            ->mapWithKeys(fn($author) => [$author->id => $author->first_name . ' ' . $author->last_name]);

        return Inertia::render('posts/Edit', [
            'post' => $post,
            'authors' => $authors,
        ]);
    }

    // Update post
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author_id' => 'required|integer|exists:authors,id',
            'published' => 'boolean',
        ]);

        $post->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'author_id' => $data['author_id'],
            'published' => $data['published'] ?? false,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    // Delete post
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
}
