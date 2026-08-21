<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** "إيه اللي غيّرني؟" — a running log of realizations that actually changed something. */
class DiaryChange extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'body'];

    protected $casts = [
        'body' => 'encrypted', // sensitive
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
