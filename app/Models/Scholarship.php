<?php

namespace App\Models;

use App\Enums\ScholarshipStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Scholarship extends Model
{
    use HasFactory;

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

    /** Days left until the application deadline (negative = passed). */
    public function daysToDeadline(): ?int
    {
        if (! $this->apply_to) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->apply_to, false);
    }
}
