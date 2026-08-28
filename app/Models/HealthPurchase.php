<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A logged "junk food" purchase (chips, instant noodles, ...) — lets the
 * module show "how many days since the last one" as a streak.
 */
class HealthPurchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'item', 'note'];

    protected $casts = [
        'date' => 'date',
        'note' => 'encrypted',
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
