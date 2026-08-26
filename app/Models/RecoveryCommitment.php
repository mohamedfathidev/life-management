<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** "حاجات لازم تلتزم بيها" — a flat list of personal rules/commitments. */
class RecoveryCommitment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'sort_order'];

    protected $casts = [
        'description' => 'encrypted', // sensitive
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
