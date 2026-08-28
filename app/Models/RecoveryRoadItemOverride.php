<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A per-user edit/hide applied to a real-data item shown on a "قبل الوقوع
 * تذكر" road (a trigger note, a damage, a dream, ...) — lets them tidy up
 * what shows on the road without touching the underlying record, which is
 * still used elsewhere in the app (setbacks list, damages page, ...).
 */
class RecoveryRoadItemOverride extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'source_key', 'hidden', 'body'];

    protected $casts = [
        'hidden' => 'boolean',
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
