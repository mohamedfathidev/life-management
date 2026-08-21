<?php

namespace App\Models;

use App\Enums\RecoveryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Recovery extends Model
{
    use HasFactory;

    protected $table = 'recoveries';

    protected $fillable = [
        'user_id', 'goal_id', 'title', 'description', 'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => RecoveryStatus::class,
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
        return $this->hasMany(RecoveryLog::class)->orderByDesc('date');
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', RecoveryStatus::Active);
    }

    // ---------------------------------------------------------------------
    // Streak logic — resets to zero on each setback (days since last setback)
    // ---------------------------------------------------------------------

    /** Date the current clean streak counts from (last setback, or start). */
    public function streakSince(): Carbon
    {
        $lastSetback = $this->logs()
            ->where('is_setback', true)
            ->max('date');

        $since = $lastSetback ? Carbon::parse($lastSetback) : $this->start_date;

        return $since->gt($this->start_date) ? $since : $this->start_date;
    }

    /**
     * The date the streak should be measured against: today while the period
     * is still running, or its end_date once that has passed — so a finished
     * period stops accruing streak days it never actually had.
     */
    protected function asOfDate(): Carbon
    {
        $today = Carbon::today();

        return ($this->end_date && $this->end_date->lt($today)) ? $this->end_date->copy() : $today;
    }

    /** Current clean streak in whole days. */
    public function currentStreakDays(): int
    {
        return (int) $this->streakSince()->diffInDays($this->asOfDate());
    }

    public function setbackCount(): int
    {
        return $this->logs()->where('is_setback', true)->count();
    }

    /** Total distinct clean days logged. */
    public function cleanDaysCount(): int
    {
        return $this->logs()->where('is_setback', false)->distinct('date')->count('date');
    }

    /**
     * Clean periods as separate segments: every stretch without a setback is its
     * own period, closed by the next setback. A closed period keeps its length
     * forever — later setbacks never erase it or bleed into it.
     *
     * @return Collection<int, object{from: Carbon, to: Carbon, days: int, is_current: bool}>
     */
    public function cleanPeriods(): Collection
    {
        // Ascending order matters: the logs() relation defaults to date DESC.
        $setbacks = $this->logs()
            ->where('is_setback', true)
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date))
            ->sort()
            ->values();

        $today = $this->asOfDate();
        $periods = collect();
        $cursor = $this->start_date->copy();

        foreach ($setbacks as $setback) {
            if ($setback->lte($cursor)) {
                continue; // duplicate or out-of-range setback: ignore safely
            }

            $periods->push((object) [
                'from' => $cursor->copy(),
                'to' => $setback->copy(),
                'days' => (int) $cursor->diffInDays($setback),
                'is_current' => false,
            ]);
            $cursor = $setback->copy();
        }

        // The still-running period since the latest setback (or since the start).
        $periods->push((object) [
            'from' => $cursor->copy(),
            'to' => $today->copy(),
            'days' => (int) $cursor->diffInDays($today),
            'is_current' => true,
        ]);

        return $periods->filter(fn ($period) => $period->days > 0)->values();
    }

    /** Days of the most recently *completed* clean period (closed by a setback). */
    public function lastCleanPeriodDays(): int
    {
        $completed = $this->cleanPeriods()->reject(fn ($period) => $period->is_current);

        return (int) ($completed->isEmpty() ? 0 : $completed->last()->days);
    }

    /** Longest clean streak across the whole history, in days. */
    public function bestStreakDays(): int
    {
        return (int) $this->cleanPeriods()->max('days');
    }

    /** Total length of this period in days: planned (start→end), or elapsed (start→today) when open-ended. */
    public function periodTotalDays(): int
    {
        $end = $this->end_date ?? Carbon::today();

        return (int) $this->start_date->diffInDays($end) + 1;
    }

    /** Remaining days in the period (if end_date is set). */
    public function remainingDays(): ?int
    {
        if (! $this->end_date) {
            return null; // Period is open-ended
        }

        $today = Carbon::today();

        // If end date has passed, return 0
        if ($this->end_date->lt($today)) {
            return 0;
        }

        // Return days remaining including today
        return (int) $today->diffInDays($this->end_date, false) + 1;
    }
}
