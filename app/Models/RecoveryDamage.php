<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * "أضرار الإدمان" — a damage caused by the addiction. Main damages hold
 * sub-damages (one level). `degree` (0–100) drives how red its circle looks.
 */
class RecoveryDamage extends Model
{
    use HasFactory;

    protected $table = 'recovery_damages';

    protected $fillable = [
        'user_id', 'parent_id', 'title', 'icon', 'description', 'degree', 'life_without',
    ];

    protected $casts = [
        'degree' => 'integer',
        'description' => 'encrypted', // sensitive
        'life_without' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderByDesc('degree');
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeMain(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Hue for the circle color: 150 (green) at 0% → 0 (red) at 100%. */
    public function hue(): int
    {
        return (int) max(0, round(150 - $this->degree * 1.5));
    }

    protected function asJson($value, $flags = 0)
    {
        return parent::asJson($value, $flags | JSON_UNESCAPED_UNICODE);
    }
}
