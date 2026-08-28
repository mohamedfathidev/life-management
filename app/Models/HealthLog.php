<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One day's health checklist: ate healthy, slept early, woke early, kept
 * the phone away during sleep. One row per user per day.
 */
class HealthLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'healthy_eating', 'slept_early', 'woke_early', 'phone_away_sleep'];

    protected $casts = [
        'date' => 'date',
        'healthy_eating' => 'boolean',
        'slept_early' => 'boolean',
        'woke_early' => 'boolean',
        'phone_away_sleep' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }
}
