<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A health harm the user wants to stay aware of (staying up late, excessive
 * phone use, ...), weighted 0–100 by how dangerous it is — mirrors
 * RecoveryDamage's "degree" pattern.
 */
class HealthHarm extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'note', 'severity'];

    protected $casts = [
        'note' => 'encrypted',
        'severity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Hue for a severity indicator: 150 (green) at 0% → 0 (red) at 100%. */
    public function hue(): int
    {
        return (int) max(0, round(150 - $this->severity * 1.5));
    }
}
