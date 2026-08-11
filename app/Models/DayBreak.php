<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayBreak extends Model
{
    use HasFactory;

    protected $fillable = ['day_id', 'started_at', 'ended_at', 'note'];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    public function isOngoing(): bool
    {
        return $this->ended_at === null;
    }

    public function durationMinutes(): int
    {
        $end = $this->ended_at ?? now();

        return (int) $this->started_at->diffInMinutes($end);
    }
}
