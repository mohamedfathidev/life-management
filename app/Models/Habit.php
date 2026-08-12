<?php

namespace App\Models;

use App\Enums\HabitType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'goal_id', 'title', 'type',
        'start_date', 'end_date', 'color', 'is_archived', 'position',
    ];

    protected $casts = [
        'type' => HabitType::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'is_archived' => 'boolean',
        'position' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    // ---------------------------------------------------------------------
    // Period window
    // ---------------------------------------------------------------------

    public function isIntermittent(): bool
    {
        return $this->type === HabitType::Intermittent;
    }

    /** The last day that counts toward stats so far (today, clamped to the period). */
    public function statsEnd(): Carbon
    {
        $today = Carbon::today();

        if ($this->isIntermittent() && $this->end_date && $this->end_date->lt($today)) {
            return $this->end_date->copy();
        }

        return $today;
    }

    /** Total days in an intermittent period (null for recurring). */
    public function periodDays(): ?int
    {
        if (! $this->isIntermittent() || ! $this->end_date) {
            return null;
        }

        return (int) $this->start_date->diffInDays($this->end_date) + 1;
    }

    // ---------------------------------------------------------------------
    // Completion helpers
    // ---------------------------------------------------------------------

    /** Whether the habit's window includes the given date (so it's "due" then). */
    public function isActiveOn(Carbon $date): bool
    {
        if ($this->start_date && $date->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $date->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function isDoneOn(Carbon|string $date): bool
    {
        $date = $date instanceof Carbon ? $date->toDateString() : $date;

        if ($this->relationLoaded('logs')) {
            return $this->logs->contains(fn (HabitLog $log) => $log->date->toDateString() === $date);
        }

        return $this->logs()->whereDate('date', $date)->exists();
    }

    /** Days elapsed in the window up to today (inclusive). */
    public function applicableDays(): int
    {
        $end = $this->statsEnd();

        if ($end->lt($this->start_date)) {
            return 0; // hasn't started yet
        }

        return (int) $this->start_date->diffInDays($end) + 1;
    }

    /** Number of days actually completed within the window. */
    public function doneCount(): int
    {
        return $this->logs()
            ->whereBetween('date', [$this->start_date->toDateString(), $this->statsEnd()->toDateString()])
            ->count();
    }

    /** Days that passed without completion. */
    public function missedCount(): int
    {
        return max(0, $this->applicableDays() - $this->doneCount());
    }

    /** Adherence 0–100 = done ÷ applicable days so far. */
    public function adherencePercent(): int
    {
        $applicable = $this->applicableDays();

        return $applicable > 0 ? (int) round($this->doneCount() / $applicable * 100) : 0;
    }

    /** Consecutive-day streak ending today or yesterday. */
    public function currentStreak(): int
    {
        $dates = $this->completedDates();

        if ($dates->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();
        $cursor = $dates->contains($today->toDateString())
            ? $today->copy()
            : $today->copy()->subDay();

        $streak = 0;
        while ($dates->contains($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    /** Longest consecutive-day streak across the whole history. */
    public function bestStreak(): int
    {
        $dates = $this->completedDates()->sort()->values();

        $best = 0;
        $run = 0;
        $prev = null;

        foreach ($dates as $d) {
            $day = Carbon::parse($d);
            if ($prev && $prev->copy()->addDay()->toDateString() === $day->toDateString()) {
                $run++;
            } else {
                $run = 1;
            }
            $best = max($best, $run);
            $prev = $day;
        }

        return $best;
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private function completedDates(): \Illuminate\Support\Collection
    {
        $logs = $this->relationLoaded('logs') ? $this->logs : $this->logs()->get();

        return $logs->map(fn (HabitLog $log) => $log->date->toDateString())->unique()->values();
    }
}
