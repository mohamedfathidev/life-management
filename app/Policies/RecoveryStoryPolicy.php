<?php

namespace App\Policies;

use App\Models\RecoveryStory;
use App\Models\User;

class RecoveryStoryPolicy
{
    public function view(User $user, RecoveryStory $story): bool
    {
        return $user->id === $story->user_id;
    }

    public function update(User $user, RecoveryStory $story): bool
    {
        return $user->id === $story->user_id;
    }

    public function delete(User $user, RecoveryStory $story): bool
    {
        return $user->id === $story->user_id;
    }
}
