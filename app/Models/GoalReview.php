<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'goal_id', 'improvement_percent',
        'shortcomings', 'strengths', 'note', 'closed_on',
    ];

    protected $casts = [
        'improvement_percent' => 'integer',
        'shortcomings' => 'array',
        'strengths' => 'array',
        'closed_on' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
