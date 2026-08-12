<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DreamPath extends Model
{
    use HasFactory;

    protected $fillable = ['dream_id', 'title', 'position'];

    public function dream(): BelongsTo
    {
        return $this->belongsTo(Dream::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(DreamMilestone::class)->orderBy('position')->orderBy('id');
    }
}
