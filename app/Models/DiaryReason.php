<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * "ليه مبتغيرش… ولا حياتي بتتغير؟" — a root-cause reason, optionally nested
 * under another reason to form a real tree instead of a flat list.
 */
class DiaryReason extends Model
{
    use HasFactory;

    protected $table = 'diary_reasons';

    protected $fillable = ['user_id', 'parent_id', 'body', 'sort_order'];

    protected $casts = [
        'body' => 'encrypted', // sensitive
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
