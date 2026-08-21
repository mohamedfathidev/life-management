<?php

namespace App\Policies;

use App\Models\RecoveryDream;
use App\Models\User;

class RecoveryDreamPolicy
{
    public function update(User $user, RecoveryDream $dream): bool
    {
        return $user->id === $dream->user_id;
    }

    public function delete(User $user, RecoveryDream $dream): bool
    {
        return $user->id === $dream->user_id;
    }
}
