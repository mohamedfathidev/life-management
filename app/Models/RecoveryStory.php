<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "حكايات التعافي" — a private diary entry for the recovery journey,
 * optionally linked to a recovery period.
 */
class RecoveryStory extends Model
{
    use HasFactory;

    protected $table = 'recovery_stories';

    protected $fillable = [
        'user_id', 'recovery_id', 'date', 'title', 'content', 'mood', 'tags',
    ];

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

    public function recovery(): BelongsTo
    {
        return $this->belongsTo(Recovery::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    protected function asJson($value, $flags = 0)
    {
        return parent::asJson($value, $flags | JSON_UNESCAPED_UNICODE);
    }
}
