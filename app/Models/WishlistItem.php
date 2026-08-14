<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    protected $fillable = ['user_id', 'transaction_id', 'title', 'estimated_price', 'importance', 'note', 'is_bought'];

    protected $casts = [
        'estimated_price' => 'decimal:2',
        'is_bought' => 'boolean',
    ];

    /** Importance levels: label, badge color, and sort weight. */
    public const IMPORTANCE = [
        'critical' => ['label' => 'ضرورية جدًا', 'color' => 'danger', 'weight' => 4],
        'high' => ['label' => 'مهمة', 'color' => 'warning', 'weight' => 3],
        'medium' => ['label' => 'عادية', 'color' => 'primary', 'weight' => 2],
        'low' => ['label' => 'تقدر تستنى', 'color' => 'ink-soft', 'weight' => 1],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** @return array{label:string,color:string,weight:int} */
    public function importanceMeta(): array
    {
        return self::IMPORTANCE[$this->importance] ?? self::IMPORTANCE['medium'];
    }
}
