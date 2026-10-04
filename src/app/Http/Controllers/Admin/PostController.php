<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->with(['user', 'club'])
            ->withCount('comments')
            ->latest()
            ->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return back()->with('success', 'Postingan dihapus.');
    }
}
