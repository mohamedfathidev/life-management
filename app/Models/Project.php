<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'goal_id', 'title', 'pitch', 'why', 'url', 'status',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ProjectStep::class)->orderBy('position');
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function progressPercent(): int
    {
        $total = $this->steps()->count();

        if ($total === 0) {
            return 0;
        }

        return (int) round($this->steps()->where('is_done', true)->count() / $total * 100);
    }

    public function currentStep(): ?ProjectStep
    {
        return $this->steps()->where('is_done', false)->first();
    }
}
