<?php

namespace App\Models;

use App\Enums\ScholarshipStage;
use App\Models\Concerns\HasItemDocuments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerActivity extends Model
{
    use HasFactory;
    use HasItemDocuments;

    protected $fillable = [
        'user_id', 'title', 'organization', 'applied_via', 'start_date', 'end_date',
        'hours', 'description', 'stage', 'submitted_on', 'decided_on', 'rejection_reason', 'interview_focus',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_on' => 'date',
        'decided_on' => 'date',
        'hours' => 'integer',
        'stage' => ScholarshipStage::class, // shares the scholarship pipeline stations
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Current activities = accepted ones you're actually doing. */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('stage', ScholarshipStage::Accepted);
    }

    /** Applications still moving through the pipeline (or rejected). */
    public function scopeApplications(Builder $query): Builder
    {
        return $query->where('stage', '!=', ScholarshipStage::Accepted);
    }

    public function isClosed(): bool
    {
        return in_array($this->stage, [ScholarshipStage::Accepted, ScholarshipStage::Rejected], true);
    }
}
