<?php

namespace App\Models;

use App\Models\Concerns\HasItemDocuments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipDocument extends Model
{
    use HasItemDocuments;

    protected $fillable = ['user_id', 'name', 'is_ready', 'file_path', 'original_name', 'position'];

    protected $casts = [
        'is_ready' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function hasFile(): bool
    {
        return (bool) $this->file_path;
    }
}
