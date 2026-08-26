<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** "أفكار تراودني" — a running log of ideas/thoughts worth capturing before they slip. */
class RecoveryIdea extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'body', 'action_taken'];

    protected $casts = [
        'body' => 'encrypted', // sensitive
        'action_taken' => 'encrypted', // sensitive
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
