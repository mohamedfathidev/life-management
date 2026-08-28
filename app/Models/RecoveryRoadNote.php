<?php

namespace App\Models;

use App\Enums\RecoveryRoad;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A user-added stop on their own "قبل الوقوع تذكر" road — extends the
 * auto-pulled real-data items with things only they'd think to write.
 */
class RecoveryRoadNote extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'road', 'stage', 'body'];

    protected $casts = [
        'road' => RecoveryRoad::class,
        'body' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeRoad(Builder $query, RecoveryRoad|string $road): Builder
    {
        return $query->where('road', $road instanceof RecoveryRoad ? $road->value : $road);
    }

    public function scopeStage(Builder $query, string $stage): Builder
    {
        return $query->where('stage', $stage);
    }
}
