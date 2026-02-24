<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        $posts = Post::latest()->with('user', 'categories')->filter(request(['search', 'category']))->paginate(5);
        $categories = Category::withCount('posts')->get();
        return view('posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'categories' => 'required|array'
        ]);

        $post = new Post($request->only('title', 'body'));
        $post->user_id = Auth::id();
        $post->save();

        $post->categories()->attach($request->categories);

        return redirect()->route('posts.index')->with('success', 'Пост успешно создан!');
    }

    public function show(Post $post)
    {
        $categories = Category::withCount('posts')->get();
        return view('posts.show', compact('post', 'categories'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'categories' => 'required|array'
        ]);

        $post->update($request->only('title', 'body'));
        $post->categories()->sync($request->categories);

        return redirect()->route('posts.index')->with('success', 'Пост успешно обновлен!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Пост успешно удален!');
    }
}
