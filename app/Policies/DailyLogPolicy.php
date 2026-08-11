<?php

namespace App\Policies;

use App\Models\DailyLog;
use App\Models\User;

class DailyLogPolicy
{
    public function view(User $user, DailyLog $log): bool
    {
        return $user->id === $log->user_id;
    }

    public function update(User $user, DailyLog $log): bool
    {
        return $user->id === $log->user_id;
    }

    public function delete(User $user, DailyLog $log): bool
    {
        return $user->id === $log->user_id;
    }
}
