<?php

namespace App\Policies;

use App\Models\ScholarshipTopic;
use App\Models\User;

class ScholarshipTopicPolicy
{
    public function update(User $user, ScholarshipTopic $topic): bool
    {
        return $user->id === $topic->user_id;
    }

    public function delete(User $user, ScholarshipTopic $topic): bool
    {
        return $user->id === $topic->user_id;
    }
}
