<?php

namespace App\Policies;

use App\Models\RecoveryDamage;
use App\Models\User;

class RecoveryDamagePolicy
{
    public function view(User $user, RecoveryDamage $damage): bool
    {
        return $user->id === $damage->user_id;
    }

    public function update(User $user, RecoveryDamage $damage): bool
    {
        return $user->id === $damage->user_id;
    }

    public function delete(User $user, RecoveryDamage $damage): bool
    {
        return $user->id === $damage->user_id;
    }
}
