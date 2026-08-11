<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function view(User $user, JobApplication $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function update(User $user, JobApplication $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function delete(User $user, JobApplication $job): bool
    {
        return $user->id === $job->user_id;
    }
}
