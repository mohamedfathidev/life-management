<?php

namespace App\Models;

use App\Enums\DayStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'week_id', 'date', 'status',
        'started_at', 'ended_at', 'rating', 'reflection',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => DayStatus::class,
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'rating' => 'integer',
    ];

    // ---------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('position')->orderBy('id');
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(DayBreak::class)->orderBy('started_at');
    }

    // ---------------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------------

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    // ---------------------------------------------------------------------
    // State helpers
    // ---------------------------------------------------------------------

    public function isStarted(): bool
    {
        return $this->started_at !== null;
    }

    public function isClosed(): bool
    {
        return $this->status === DayStatus::Closed;
    }

    /** The currently-running break, if any. */
    public function ongoingBreak(): ?DayBreak
    {
        return $this->breaks->firstWhere('ended_at', null)
            ?? $this->breaks()->whereNull('ended_at')->first();
    }

    // ---------------------------------------------------------------------
    // Time & progress calculations
    // ---------------------------------------------------------------------

    /** Total minutes spent on breaks (ongoing break counted up to now). */
    public function breakMinutes(): int
    {
        return (int) $this->breaks->sum(function (DayBreak $break) {
            $end = $break->ended_at ?? now();

            return $break->started_at->diffInMinutes($end);
        });
    }

    /**
     * Actual worked minutes: (end − start) − breaks.
     * Uses now() as the end when the day is started but not ended.
     */
    public function workedMinutes(): int
    {
        if (! $this->started_at) {
            return 0;
        }

        $end = $this->ended_at ?? now();
        $gross = (int) $this->started_at->diffInMinutes($end);

        return max(0, $gross - $this->breakMinutes());
    }

    /** Human label like "5س 30د". */
    public function workedHoursLabel(): string
    {
        $minutes = $this->workedMinutes();

        return sprintf('%dس %02dد', intdiv($minutes, 60), $minutes % 60);
    }

    /** Day completion 0–100 = average of task progress. */
    public function completionPercent(): int
    {
        $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->get();

        if ($tasks->isEmpty()) {
            return 0;
        }

        return (int) round($tasks->avg('progress'));
    }

    public function scopeForDate(Builder $query, Carbon|string $date): Builder
    {
        return $query->whereDate('date', $date);
    }
}
