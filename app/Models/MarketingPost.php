<?php

namespace App\Models;

use App\Enums\MarketingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'platform', 'topic', 'content', 'status', 'scheduled_for', 'link',
    ];

    protected $casts = [
        'status' => MarketingStatus::class,
        'scheduled_for' => 'date',
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
