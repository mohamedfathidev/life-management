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

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Filter topics that contain the given tag (JSON array contains). */
    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->whereJsonContains('tags', $tag);
    }
}
