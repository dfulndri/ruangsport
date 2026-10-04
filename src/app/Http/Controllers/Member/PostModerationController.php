<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

/** Hapus posting/komentar oleh penulisnya, pengelola klub (feed klub), atau admin. */
class PostModerationController extends Controller
{
    public function destroyPost(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return back()->with('success', 'Postingan dihapus.');
    }

    public function destroyComment(Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Komentar dihapus.');
    }
}
