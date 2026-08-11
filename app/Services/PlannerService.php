<?php

namespace App\Services;

use App\Enums\DayStatus;
use App\Models\Day;
use App\Models\User;
use App\Models\Week;
use Illuminate\Support\Carbon;

/**
 * Resolves (find-or-create) the Week and Day records for a date, so the rest
 * of the app never has to worry about whether they exist yet.
 */
class PlannerService
{
    public function resolveWeek(User $user, Carbon $date): Week
    {
        [$start, $end] = Week::boundariesFor($date);

        $week = Week::where('user_id', $user->id)
            ->whereDate('start_date', $start)
            ->first();

        return $week ?? Week::create([
            'user_id' => $user->id,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
        ]);
    }

    public function resolveDay(User $user, Carbon $date): Day
    {
        $day = Day::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        if ($day) {
            return $day;
        }

        $week = $this->resolveWeek($user, $date);

        return Day::create([
            'user_id' => $user->id,
            'week_id' => $week->id,
            'date' => $date->toDateString(),
            'status' => DayStatus::Open->value,
        ]);
    }
}
