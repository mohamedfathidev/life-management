<?php

namespace App\Models;

use App\Enums\GoalCategory;
use App\Enums\GoalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'parent_id', 'title', 'category', 'description',
        'color', 'icon', 'status', 'start_date', 'target_date',
    ];

    protected $casts = [
        'category' => GoalCategory::class,
        'status' => GoalStatus::class,
        'start_date' => 'date',
        'target_date' => 'date',
    ];

    // ---------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    /** Planner tasks linked to this goal. */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /** The parent goal, if this is a sub-goal. */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Sub-goals contained within this goal. */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /** The closing review (shortcomings/strengths/improvement), one per goal. */
    public function review(): HasOne
    {
        return $this->hasOne(GoalReview::class);
    }

    // ---------------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------------

    /** Scope: only goals owned by the given user. */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', GoalStatus::Active);
    }

    /** Scope: only top-level goals (not sub-goals). */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    // ---------------------------------------------------------------------
    // Computed helpers (dates & progress)
    // ---------------------------------------------------------------------

    public function isSubGoal(): bool
    {
        return $this->parent_id !== null;
    }

    /** Whether a task on the given date is allowed inside this goal's window. */
    public function acceptsDate(Carbon $date): bool
    {
        if ($this->start_date && $date->lt($this->start_date)) {
            return false;
        }

        if ($this->target_date && $date->gt($this->target_date)) {
            return false;
        }

        return true;
    }

    /** Arabic explanation for why a date is outside this goal's window. */
    public function rangeMessage(Carbon $date): string
    {
        if ($this->start_date && $date->lt($this->start_date)) {
            return "اليوم ({$date->translatedFormat('j M')}) قبل بداية الهدف ({$this->start_date->translatedFormat('j M')}).";
        }

        return "اليوم ({$date->translatedFormat('j M')}) بعد نهاية الهدف ({$this->target_date?->translatedFormat('j M')}).";
    }

    /**
     * A big (top-level) goal can be closed with a review only from its last
     * day onward, and only once, while it isn't already completed.
     */
    public function canClose(): bool
    {
        return $this->parent_id === null
            && $this->status !== GoalStatus::Completed
            && $this->target_date !== null
            && Carbon::today()->gte($this->target_date);
    }

    /**
     * Whole days left until the end date (target_date).
     * Negative when overdue, null when there is no end date.
     */
    public function remainingDays(): ?int
    {
        if (! $this->target_date) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->target_date, false);
    }

    public function isOverdue(): bool
    {
        $remaining = $this->remainingDays();

        return $remaining !== null
            && $remaining < 0
            && $this->status !== GoalStatus::Completed;
    }

    /** Whole weeks left until the end date (rounded up). Null when no end date. */
    public function remainingWeeks(): ?int
    {
        $days = $this->remainingDays();

        if ($days === null || $days <= 0) {
            return $days === null ? null : 0;
        }

        return (int) ceil($days / 7);
    }

    /**
     * Overall completion 0–100 of this goal, computed bottom-up:
     *   • A goal WITH sub-goals  → average of its sub-goals' completion (recursive).
     *   • A leaf goal            → 100 if marked completed, else the average
     *                              progress of its linked planner tasks, else 0.
     * This unifies sub-goals + daily tasks into one meaningful number.
     */
    public function completionPercent(): int
    {
        $children = $this->relationLoaded('children') ? $this->children : $this->children()->get();

        if ($children->isNotEmpty()) {
            return (int) round($children->avg(fn (Goal $child) => $child->completionPercent()));
        }

        if ($this->status === GoalStatus::Completed) {
            return 100;
        }

        $tasks = $this->relationLoaded('tasks') ? $this->tasks : $this->tasks()->get();

        if ($tasks->isNotEmpty()) {
            return (int) round($tasks->avg('progress'));
        }

        return 0;
    }

    /** Total planned duration in days (start_date → target_date). */
    public function durationDays(): ?int
    {
        if (! $this->start_date || ! $this->target_date) {
            return null;
        }

        return (int) $this->start_date->diffInDays($this->target_date, false);
    }

    /**
     * Time-based progress 0–100 (how much of the start→end window has elapsed).
     * Null when the window can't be computed.
     */
    public function timeProgressPercent(): ?int
    {
        $duration = $this->durationDays();

        if ($duration === null || $duration <= 0) {
            return null;
        }

        $elapsed = (int) $this->start_date->diffInDays(Carbon::today(), false);

        return (int) max(0, min(100, round($elapsed / $duration * 100)));
    }

    /** Progress based on completed sub-goals, 0–100. Null when no sub-goals. */
    public function childrenProgressPercent(): ?int
    {
        $total = $this->children()->count();

        if ($total === 0) {
            return null;
        }

        $done = $this->children()->where('status', GoalStatus::Completed)->count();

        return (int) round($done / $total * 100);
    }
}
