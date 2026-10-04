<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy extends BasePolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    /** Penulis, atau pengelola klub bila posting ada di feed klub (moderasi). */
    public function delete(User $user, Post $post): bool
    {
        return $post->user_id === $user->id
            || $this->managesClub($user, $post->club_id);
    }
}
