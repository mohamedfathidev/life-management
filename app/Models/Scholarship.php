<?php

namespace App\Models;

use App\Enums\ScholarshipStage;
use App\Models\Concerns\HasItemDocuments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Scholarship extends Model
{
    use HasFactory;
    use HasItemDocuments;

    protected $fillable = [
        'user_id', 'name', 'institution', 'apply_from', 'apply_to',
        'requirements', 'notes', 'stage', 'submitted_on', 'decided_on', 'rejection_reason', 'interview_focus',
    ];

    protected $casts = [
        'apply_from' => 'date',
        'apply_to' => 'date',
        'submitted_on' => 'date',
        'decided_on' => 'date',
        'stage' => ScholarshipStage::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function isClosed(): bool
    {
        return in_array($this->stage, [ScholarshipStage::Accepted, ScholarshipStage::Rejected], true);
    }

    /**
     * The application window status relative to today:
     *   open      → you can apply now
     *   upcoming  → its window hasn't opened yet
     *   closed    → the deadline has passed
     */
    public function windowStatus(): string
    {
        $today = Carbon::today();

        if ($this->apply_to && $today->gt($this->apply_to)) {
            return 'closed';
        }

        if ($this->apply_from && $today->lt($this->apply_from)) {
            return 'upcoming';
        }

        return 'open';
    }

    public function windowLabel(): string
    {
        return match ($this->windowStatus()) {
            'open' => 'مفتوحة الآن',
            'upcoming' => 'لسه ماجاش معادها',
            default => 'اتقفلت',
        };
    }

    /** Days left until the application deadline (negative = passed). */
    public function daysToDeadline(): ?int
    {
        if (! $this->apply_to) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->apply_to, false);
    }

    /** Days until the application window opens (positive = future). */
    public function daysToOpen(): ?int
    {
        if (! $this->apply_from) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->apply_from, false);
    }
}
