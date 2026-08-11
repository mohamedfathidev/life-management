<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeLog extends Model
{
    use HasFactory;

    protected $fillable = ['challenge_id', 'date'];

    protected $casts = ['date' => 'date'];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }
}
