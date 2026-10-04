<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;

class CommunityController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->with(['user', 'club', 'comments.user', 'comments.post'])
            ->latest()
            ->paginate(10);

        return view('community.index', compact('posts'));
    }
}
