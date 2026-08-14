<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerResource extends Model
{
    protected $fillable = ['user_id', 'context', 'title', 'url', 'image_path', 'type', 'note'];

    public function hasImage(): bool
    {
        return (bool) $this->image_path;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }
}
