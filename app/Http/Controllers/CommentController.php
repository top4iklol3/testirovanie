<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $comment = new Comment($request->only('body'));
        $comment->user_id = Auth::id();
        $post->comments()->save($comment);

        return redirect()->route('posts.show', $post);
    }
}
