<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DreamMilestone extends Model
{
    use HasFactory;

    protected $fillable = ['dream_path_id', 'title', 'note', 'is_done', 'target_date', 'position'];

    protected $casts = [
        'is_done' => 'boolean',
        'target_date' => 'date',
    ];

    public function path(): BelongsTo
    {
        return $this->belongsTo(DreamPath::class, 'dream_path_id');
    }
}
