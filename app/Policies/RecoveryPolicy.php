<?php

namespace App\Policies;

use App\Models\Recovery;
use App\Models\User;

class RecoveryPolicy
{
    public function view(User $user, Recovery $recovery): bool
    {
        return $user->id === $recovery->user_id;
    }

    public function update(User $user, Recovery $recovery): bool
    {
        return $user->id === $recovery->user_id;
    }

    public function delete(User $user, Recovery $recovery): bool
    {
        return $user->id === $recovery->user_id;
    }
}
