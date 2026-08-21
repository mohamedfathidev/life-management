<?php

namespace App\Policies;

use App\Models\RecoveryChange;
use App\Models\User;

class RecoveryChangePolicy
{
    public function view(User $user, RecoveryChange $change): bool
    {
        return $user->id === $change->user_id;
    }

    public function update(User $user, RecoveryChange $change): bool
    {
        return $user->id === $change->user_id;
    }

    public function delete(User $user, RecoveryChange $change): bool
    {
        return $user->id === $change->user_id;
    }
}
