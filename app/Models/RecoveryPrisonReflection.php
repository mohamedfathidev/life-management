<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "ماذا لو لم أدخل هذا السجن؟" — one reflection per user (like the pledge):
 * how many years the addiction cycle has cost them, and — in their own
 * words — what their life could have looked like without it.
 */
class RecoveryPrisonReflection extends Model
{
    use HasFactory;

    protected $table = 'recovery_prison_reflections';

    protected $fillable = ['user_id', 'prison_years', 'body'];

    protected $casts = [
        'prison_years' => 'integer',
        'body' => 'encrypted',
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
