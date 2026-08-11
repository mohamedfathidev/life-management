<?php

namespace App\Models;

use App\Enums\RecoveryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    /** Current clean streak in whole days. */
    public function currentStreakDays(): int
    {
        return (int) $this->streakSince()->diffInDays(Carbon::today());
    }

    public function setbackCount(): int
    {
        return $this->logs()->where('is_setback', true)->count();
    }

    /** Longest clean streak across the whole history, in days. */
    public function bestStreakDays(): int
    {
        $setbackDates = $this->logs()
            ->where('is_setback', true)
            ->orderBy('date')
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d))
            ->all();

        $cursor = $this->start_date->copy();
        $best = 0;

        foreach ($setbackDates as $date) {
            $best = max($best, (int) $cursor->diffInDays($date));
            $cursor = $date->copy();
        }

        return max($best, (int) $cursor->diffInDays(Carbon::today()));
    }
}
