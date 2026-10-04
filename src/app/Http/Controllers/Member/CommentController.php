<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('create', Comment::class);

        $data = $request->validate(
            ['body' => ['required', 'string', 'max:500']],
            ['body.required' => 'Komentar wajib diisi.', 'body.max' => 'Komentar maksimal 500 karakter.']
        );

        Comment::create($data + ['post_id' => $post->id, 'user_id' => $request->user()->id]);

        return back()->with('success', 'Komentar terkirim.');
    }
}
