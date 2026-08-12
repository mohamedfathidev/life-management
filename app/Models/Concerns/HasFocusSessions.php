<?php

namespace App\Models\Concerns;

use App\Models\FocusSession;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * Gives a model focus-mode stopwatch sessions and a helper to total the
 * seconds focused on it for a given day.
 */
trait HasFocusSessions
{
    public function focusSessions(): MorphMany
    {
        return $this->morphMany(FocusSession::class, 'focusable');
    }

    public function focusSecondsOn(Carbon|string $date): int
    {
        $date = $date instanceof Carbon ? $date->toDateString() : $date;

        return (int) $this->focusSessions()->whereDate('date', $date)->sum('seconds');
    }
}
