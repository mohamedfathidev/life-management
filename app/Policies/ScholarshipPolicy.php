<?php

namespace App\Policies;

use App\Models\Scholarship;
use App\Models\User;

class ScholarshipPolicy
{
    public function view(User $user, Scholarship $scholarship): bool
    {
        return $user->id === $scholarship->user_id;
    }

    public function update(User $user, Scholarship $scholarship): bool
    {
        return $user->id === $scholarship->user_id;
    }

    public function delete(User $user, Scholarship $scholarship): bool
    {
        return $user->id === $scholarship->user_id;
    }
}
