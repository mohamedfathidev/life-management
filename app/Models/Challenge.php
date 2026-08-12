<?php

namespace App\Models;

use App\Enums\ChallengeStatus;
use App\Models\Concerns\HasFocusSessions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Challenge extends Model
{
    use HasFactory;
    use HasFocusSessions;

    protected $fillable = [
        'user_id', 'goal_id', 'title', 'description',
        'start_date', 'duration_days', 'status', 'color',
    ];

    protected $casts = [
        'start_date' => 'date',
        'duration_days' => 'integer',
        'status' => ChallengeStatus::class,
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
        return $this->hasMany(ChallengeLog::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ChallengeStatus::Active);
    }

    // ---------------------------------------------------------------------
    // Period + progress
    // ---------------------------------------------------------------------

    public function endDate(): Carbon
    {
        return $this->start_date->copy()->addDays(max(0, $this->duration_days - 1));
    }

    /** Days from start to today (clamped to the duration), min 0. */
    public function daysElapsed(): int
    {
        if (Carbon::today()->lt($this->start_date)) {
            return 0;
        }

        $elapsed = (int) $this->start_date->diffInDays(Carbon::today()) + 1;

        return min($elapsed, $this->duration_days);
    }

    public function daysRemaining(): int
    {
        return max(0, $this->duration_days - $this->daysElapsed());
    }

    public function doneCount(): int
    {
        $logs = $this->relationLoaded('logs') ? $this->logs : $this->logs()->get();

        return $logs->unique(fn (ChallengeLog $l) => $l->date->toDateString())->count();
    }

    /** Progress toward the full duration, 0–100. */
    public function progressPercent(): int
    {
        return $this->duration_days > 0
            ? (int) round($this->doneCount() / $this->duration_days * 100)
            : 0;
    }

    public function isDoneOn(Carbon|string $date): bool
    {
        $date = $date instanceof Carbon ? $date->toDateString() : $date;

        if ($this->relationLoaded('logs')) {
            return $this->logs->contains(fn (ChallengeLog $l) => $l->date->toDateString() === $date);
        }

        return $this->logs()->whereDate('date', $date)->exists();
    }

    /** Consecutive-day streak ending today or yesterday. */
    public function currentStreak(): int
    {
        $dates = ($this->relationLoaded('logs') ? $this->logs : $this->logs()->get())
            ->map(fn (ChallengeLog $l) => $l->date->toDateString())->unique();

        if ($dates->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();
        $cursor = $dates->contains($today->toDateString()) ? $today->copy() : $today->copy()->subDay();

        $streak = 0;
        while ($dates->contains($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }
}
