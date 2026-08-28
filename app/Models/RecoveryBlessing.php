<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "النعم التي أغفل عنها" — blessings the user doesn't want to keep taking
 * for granted (health, security, a roof, ...), written in their own words.
 */
class RecoveryBlessing extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'text', 'position'];

    protected $casts = [
        'text' => 'encrypted',
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
