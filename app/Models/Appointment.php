<?php

namespace App\Models;

use App\Enums\AppointmentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'type', 'date', 'time', 'location', 'note', 'is_done'];

    protected $casts = [
        'date' => 'date',
        'type' => AppointmentType::class,
        'is_done' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function timeLabel(): ?string
    {
        return $this->time ? substr($this->time, 0, 5) : null;
    }
}
