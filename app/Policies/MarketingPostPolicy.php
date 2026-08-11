<?php

namespace App\Policies;

use App\Models\MarketingPost;
use App\Models\User;

class MarketingPostPolicy
{
    public function update(User $user, MarketingPost $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, MarketingPost $post): bool
    {
        return $user->id === $post->user_id;
    }
}
