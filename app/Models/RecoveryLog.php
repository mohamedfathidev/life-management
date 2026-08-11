<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recovery_id', 'date', 'urge_level', 'mood',
        'trigger_note', 'note', 'is_setback',
        'hardest_from', 'hardest_to',
    ];

    protected $casts = [
        'date' => 'date',
        'urge_level' => 'integer',
        'mood' => 'integer',
        'is_setback' => 'boolean',
        // sensitive fields encrypted at rest (§12)
        'trigger_note' => 'encrypted',
        'note' => 'encrypted',
    ];

    public function recovery(): BelongsTo
    {
        return $this->belongsTo(Recovery::class);
    }
}
