<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return redirect()->back();
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        abort_unless(auth()->user()?->is_admin, 403);

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully.');
    }
}
