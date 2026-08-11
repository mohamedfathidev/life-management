<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuranLog extends Model
{
    use HasFactory;

    /** Total pages in the standard Madani mushaf. */
    public const MUSHAF_PAGES = 604;

    protected $fillable = [
        'user_id', 'date', 'from_surah', 'from_ayah', 'to_surah', 'to_ayah', 'pages', 'note',
    ];

    protected $casts = [
        'date' => 'date',
        'from_ayah' => 'integer',
        'to_ayah' => 'integer',
        'pages' => 'integer',
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
