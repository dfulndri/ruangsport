<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy extends BasePolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    /** Penulis komentar, atau pengelola klub pemilik posting. */
    public function delete(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id
            || $this->managesClub($user, $comment->post->club_id);
    }
}
