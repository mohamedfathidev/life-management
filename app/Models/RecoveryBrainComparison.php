<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "الدماغ الإدماني vs دماغي الطبيعية" — the same point in life, contrasted
 * across what the addicted brain chases versus what the user's own,
 * healthy brain actually wants.
 */
class RecoveryBrainComparison extends Model
{
    use HasFactory;

    protected $table = 'recovery_brain_comparisons';

    protected $fillable = ['user_id', 'point', 'addictive_text', 'normal_text', 'position'];

    protected $casts = [
        'addictive_text' => 'encrypted',
        'normal_text' => 'encrypted',
        'position' => 'integer',
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
