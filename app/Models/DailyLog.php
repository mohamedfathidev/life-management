<?php

namespace App\Models;

use App\Enums\ModuleType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'goal_id', 'module_type',
        'date', 'mood', 'difficulty', 'note',
    ];

    protected $casts = [
        'module_type' => ModuleType::class,
        'date' => 'date',
        'mood' => 'integer',
        'difficulty' => 'integer',
    ];

    /** Scope: only logs owned by the given user. */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForDate(Builder $query, \DateTimeInterface|string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
