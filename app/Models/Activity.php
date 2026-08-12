<?php

namespace App\Models;

use App\Enums\ActivityStage;
use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'goal_id', 'title', 'type', 'organizer', 'location',
        'url', 'start_date', 'end_date', 'stage', 'result', 'notes',
    ];

    protected $casts = [
        'type' => ActivityType::class,
        'stage' => ActivityStage::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function isClosed(): bool
    {
        return in_array($this->stage, [ActivityStage::Done, ActivityStage::Rejected], true);
    }
}
