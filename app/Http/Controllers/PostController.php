<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia; 

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('posts/Index', [
            'posts' => Post::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('posts/Create'); 
    }

    public function store(Request $request)
    {
        Post::create($request ->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:100',
            'published' => 'boolean',
        ]));
        return redirect()->route('posts.index');

        
    }

    public function show(Post $post)
    {
        //
    }

    public function edit(Post $post)
    {
        //
    }

    public function update(Request $request, Post $post)
    {
        //
    }

    public function destroy(Post $post)
    {
        //
    }
}