<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Where the user last stopped reading — one row per user, so "اقرأ" can
 * open straight back to it.
 */
class QuranReadingPosition extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'surah_number', 'ayah_number'];

    protected $casts = [
        'surah_number' => 'integer',
        'ayah_number' => 'integer',
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
