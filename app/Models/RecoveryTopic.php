<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryTopic extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'content', 'tags', 'importance'];

    protected $casts = [
        'tags' => 'array',
        'content' => 'encrypted', // sensitive reflections
        'importance' => \App\Enums\TopicImportance::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nutritionLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MentalNutritionLog::class);
    }

    protected function asJson($value, $flags = 0)
    {
        return parent::asJson($value, $flags | JSON_UNESCAPED_UNICODE);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Filter topics that contain the given tag (JSON array contains / JSON_SEARCH). */
    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        $clean = ltrim(trim($tag), '#');
        if ($clean === '') {
            return $query;
        }

        $cleanSpace = str_replace('_', ' ', $clean);
        $cleanUnderscore = str_replace(' ', '_', $clean);

        return $query->where(function ($q) use ($clean, $cleanSpace, $cleanUnderscore) {
            $q->whereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ['%'.$clean.'%'])
              ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ['%'.$cleanSpace.'%'])
              ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", ['%'.$cleanUnderscore.'%'])
              ->orWhereJsonContains('tags', $clean)
              ->orWhereJsonContains('tags', '#'.$clean)
              ->orWhere('tags', 'like', '%'.$clean.'%');
        });
    }
}
