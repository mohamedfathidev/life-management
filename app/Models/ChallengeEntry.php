<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeEntry extends Model
{
    protected $fillable = ['shared_challenge_id', 'user_id', 'date', 'prayers', 'wird_pages', 'extras', 'points'];

    protected $casts = [
        'date' => 'date',
        'prayers' => 'array',
        'extras' => 'array',
        'wird_pages' => 'integer',
        'points' => 'integer',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(SharedChallenge::class, 'shared_challenge_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
