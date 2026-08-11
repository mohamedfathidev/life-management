<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'target', 'file_path', 'original_name', 'size'];

    protected $casts = [
        'size' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function sizeLabel(): string
    {
        $kb = ($this->size ?? 0) / 1024;

        return $kb >= 1024
            ? number_format($kb / 1024, 1).' MB'
            : number_format($kb).' KB';
    }
}
