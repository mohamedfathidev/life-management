<?php

namespace App\Models;

use App\Enums\TaskKind;
use App\Enums\TaskStatus;
use App\Models\Concerns\HasFocusSessions;
use App\Models\Concerns\HasItemDocuments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;
    use HasFocusSessions;
    use HasItemDocuments;

    protected $fillable = [
        'user_id', 'day_id', 'goal_id', 'study_track_id', 'title', 'kind', 'is_important',
        'start_time', 'end_time', 'notes', 'estimated_minutes', 'actual_minutes',
        'rating', 'progress', 'status', 'position', 'carry_count',
    ];

    protected $casts = [
        'kind' => TaskKind::class,
        'status' => TaskStatus::class,
        'is_important' => 'boolean',
        'progress' => 'integer',
        'estimated_minutes' => 'integer',
        'actual_minutes' => 'integer',
        'rating' => 'integer',
        'position' => 'integer',
        'carry_count' => 'integer',
    ];

    // ---------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function studyTrack(): BelongsTo
    {
        return $this->belongsTo(StudyTrack::class);
    }

    // ---------------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------------

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Not-yet-done tasks. */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where('status', '!=', TaskStatus::Done);
    }

    /** Backlog: tasks not assigned to any day. */
    public function scopeInPool(Builder $query): Builder
    {
        return $query->whereNull('day_id');
    }

    // ---------------------------------------------------------------------
    // Behaviour
    // ---------------------------------------------------------------------

    public function isDone(): bool
    {
        return $this->status === TaskStatus::Done;
    }

    /** Set progress (0–100) and keep the status in sync. */
    public function setProgress(int $progress): void
    {
        $progress = max(0, min(100, $progress));

        $this->progress = $progress;
        $this->status = TaskStatus::fromProgress($progress);
    }

    // ---------------------------------------------------------------------
    // Scheduling (planned start/end clock times)
    // ---------------------------------------------------------------------

    public function startLabel(): ?string
    {
        return $this->start_time ? \Illuminate\Support\Carbon::parse($this->start_time)->format('g:i A') : null;
    }

    public function endLabel(): ?string
    {
        return $this->end_time ? \Illuminate\Support\Carbon::parse($this->end_time)->format('g:i A') : null;
    }

    /** Planned duration in minutes (when both times are set and end > start). */
    public function durationMinutes(): ?int
    {
        if (! $this->start_time || ! $this->end_time) {
            return null;
        }

        $start = \Illuminate\Support\Carbon::parse($this->start_time);
        $end = \Illuminate\Support\Carbon::parse($this->end_time);

        return $end->greaterThan($start) ? (int) $start->diffInMinutes($end) : null;
    }

    /** Arabic label for the planned duration, e.g. "1 س 30 د". */
    public function durationLabel(): ?string
    {
        return self::minutesToLabel($this->durationMinutes());
    }

    /** Actual minutes: an explicit override, else derived from focus sessions. */
    public function actualMinutes(): int
    {
        return $this->actual_minutes ?? (int) round($this->focusSecondsTotal() / 60);
    }

    public function actualLabel(): ?string
    {
        return self::minutesToLabel($this->actualMinutes());
    }

    public function estimatedLabel(): ?string
    {
        return self::minutesToLabel($this->estimated_minutes);
    }

    /** Format a minutes count as an Arabic hours/minutes label. */
    public static function minutesToLabel(?int $minutes): ?string
    {
        if ($minutes === null || $minutes <= 0) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        $parts = [];
        if ($hours) {
            $parts[] = $hours.' س';
        }
        if ($mins) {
            $parts[] = $mins.' د';
        }

        return implode(' ', $parts);
    }
}
