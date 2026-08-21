<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "أحلام التعافي" — the future the user is recovering for, each paired with
 * the benefits that make it worth the fight. A vision board, not a diary.
 */
class RecoveryDream extends Model
{
    use HasFactory;

    protected $table = 'recovery_dreams';

    protected $fillable = [
        'user_id', 'recovery_id', 'icon', 'title', 'benefits', 'is_achieved', 'achieved_at',
    ];

    protected $casts = [
        'benefits' => 'array',
        'is_achieved' => 'boolean',
        'achieved_at' => 'date',
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
