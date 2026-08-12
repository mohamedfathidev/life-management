<?php

namespace App\Models;

use App\Enums\JobStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'goal_id', 'position', 'company', 'applied_via',
        'url', 'description', 'applied_on', 'deadline', 'interview_at',
        'stage', 'rejection_reason', 'company_research',
    ];

    protected $casts = [
        'applied_on' => 'date',
        'deadline' => 'date',
        'interview_at' => 'date',
        'stage' => JobStage::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function prepItems(): HasMany
    {
        return $this->hasMany(JobPrepItem::class)->orderBy('position')->orderBy('id');
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Interview prep is only relevant once an interview is reached (and not rejected). */
    public function needsInterviewPrep(): bool
    {
        return in_array($this->stage, [JobStage::Interview, JobStage::Offer], true);
    }

    public function isClosed(): bool
    {
        return in_array($this->stage, [JobStage::Offer, JobStage::Rejected], true);
    }
}
