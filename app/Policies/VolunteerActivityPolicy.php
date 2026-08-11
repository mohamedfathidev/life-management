<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VolunteerActivity;

class VolunteerActivityPolicy
{
    public function update(User $user, VolunteerActivity $activity): bool
    {
        return $user->id === $activity->user_id;
    }

    public function delete(User $user, VolunteerActivity $activity): bool
    {
        return $user->id === $activity->user_id;
    }
}
