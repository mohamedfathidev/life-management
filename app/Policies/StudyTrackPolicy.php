<?php

namespace App\Policies;

use App\Models\StudyTrack;
use App\Models\User;

class StudyTrackPolicy
{
    public function view(User $user, StudyTrack $track): bool
    {
        return $user->id === $track->user_id;
    }

    public function update(User $user, StudyTrack $track): bool
    {
        return $user->id === $track->user_id;
    }

    public function delete(User $user, StudyTrack $track): bool
    {
        return $user->id === $track->user_id;
    }
}
