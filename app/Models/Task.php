<?php

namespace App\Models;

use App\Enums\TaskKind;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'day_id', 'goal_id', 'title', 'kind',
        'progress', 'status', 'position', 'carry_count',
    ];

    protected $casts = [
        'kind' => TaskKind::class,
        'status' => TaskStatus::class,
        'progress' => 'integer',
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
}
