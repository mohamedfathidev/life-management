<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryEntry extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'goal_id', 'date', 'title', 'content', 'mood', 'tags'];

    protected $casts = [
        'date' => 'date',
        'mood' => 'integer',
        'tags' => 'array',
        'content' => 'encrypted', // sensitive
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    protected function asJson($value, $flags = 0)
    {
        return parent::asJson($value, $flags | JSON_UNESCAPED_UNICODE);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

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
