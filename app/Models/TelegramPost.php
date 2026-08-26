<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A post pulled from a public Telegram channel's preview page (t.me/s/{channel}). */
class TelegramPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'channel', 'message_id', 'content', 'image_url', 'video_url', 'post_url', 'posted_at',
    ];

    protected $casts = [
        'message_id' => 'integer',
        'posted_at' => 'datetime',
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
