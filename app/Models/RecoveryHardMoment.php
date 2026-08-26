<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "أصعب اللحظات" — a recurring trigger scenario ("لما بيحصل كذا") paired with
 * the coping plan for facing it, so the plan is ready before the moment hits.
 */
class RecoveryHardMoment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'plan'];

    protected $casts = [
        'description' => 'encrypted',
        'plan' => 'encrypted',
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
