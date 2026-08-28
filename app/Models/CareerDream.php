<?php

namespace App\Models;

use App\Enums\CareerDreamStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "أحلام الكارير" — deliberately independent of the general Dream model
 * (no paths/milestones/roads): just the dream, its status, and a free-text
 * note on how far it's gotten.
 */
class CareerDream extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'status', 'progress_note', 'position'];

    protected $casts = [
        'status' => CareerDreamStatus::class,
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
