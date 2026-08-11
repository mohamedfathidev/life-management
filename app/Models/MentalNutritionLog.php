<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalNutritionLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'recovery_topic_id', 'date', 'reflection'];

    protected $casts = [
        'date' => 'date',
        'reflection' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(RecoveryTopic::class, 'recovery_topic_id');
    }
}
