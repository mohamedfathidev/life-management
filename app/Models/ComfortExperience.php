<?php

namespace App\Models;

use App\Enums\ExperienceKind;
use App\Enums\ExperienceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComfortExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'kind', 'status', 'difficulty',
        'fear', 'reflection', 'target_date', 'done_on',
    ];

    protected $casts = [
        'kind' => ExperienceKind::class,
        'status' => ExperienceStatus::class,
        'difficulty' => 'integer',
        'target_date' => 'date',
        'done_on' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeDone(Builder $query): Builder
    {
        return $query->where('status', ExperienceStatus::Done);
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->where('status', '!=', ExperienceStatus::Done);
    }
}
