<?php

namespace App\Policies;

use App\Models\RecoveryTopic;
use App\Models\User;

class RecoveryTopicPolicy
{
    public function view(User $user, RecoveryTopic $topic): bool
    {
        return $user->id === $topic->user_id;
    }

    public function update(User $user, RecoveryTopic $topic): bool
    {
        return $user->id === $topic->user_id;
    }

    public function delete(User $user, RecoveryTopic $topic): bool
    {
        return $user->id === $topic->user_id;
    }
}
