<?php

namespace App\Policies;

use App\Models\Dream;
use App\Models\User;

class DreamPolicy
{
    public function view(User $user, Dream $dream): bool
    {
        return $user->id === $dream->user_id;
    }

    public function update(User $user, Dream $dream): bool
    {
        return $user->id === $dream->user_id;
    }

    public function delete(User $user, Dream $dream): bool
    {
        return $user->id === $dream->user_id;
    }
}
