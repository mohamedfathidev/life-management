<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryChangeStep extends Model
{
    protected $table = 'recovery_change_steps';

    protected $fillable = [
        'recovery_change_id', 'title', 'is_done', 'done_at', 'sort_order',
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'done_at' => 'date',
    ];

    public function change(): BelongsTo
    {
        return $this->belongsTo(RecoveryChange::class, 'recovery_change_id');
    }
}
