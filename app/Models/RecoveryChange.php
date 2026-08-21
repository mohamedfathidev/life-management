<?php

namespace App\Models;

use App\Enums\RecoveryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * "تغييرات جذرية في شخصيتي" — a concrete personality trait or habit being
 * reworked, broken down into trackable steps rather than left as a wish.
 */
class RecoveryChange extends Model
{
    use HasFactory;

    protected $table = 'recovery_changes';

    protected $fillable = [
        'user_id', 'recovery_id', 'icon', 'title', 'why', 'status', 'started_at', 'target_date',
    ];

    protected $casts = [
        'status' => RecoveryStatus::class,
        'started_at' => 'date',
        'target_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recovery(): BelongsTo
    {
        return $this->belongsTo(Recovery::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecoveryChangeStep::class)->orderBy('sort_order');
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Share of steps marked done, 0–100. Zero when there are no steps yet. */
    public function progressPercent(): int
    {
        $total = $this->steps()->count();

        if ($total === 0) {
            return 0;
        }

        $done = $this->steps()->where('is_done', true)->count();

        return (int) round($done / $total * 100);
    }

    public function daysSinceStart(): int
    {
        return (int) $this->started_at->diffInDays(Carbon::today());
    }

    /** Days left until the target date, or null when open-ended. */
    public function daysRemaining(): ?int
    {
        if (! $this->target_date) {
            return null;
        }

        $today = Carbon::today();

        if ($this->target_date->lt($today)) {
            return 0;
        }

        return (int) $today->diffInDays($this->target_date, false);
    }
}
