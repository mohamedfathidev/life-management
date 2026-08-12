<?php

namespace App\Models;

use App\Enums\PrayerState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerDay extends Model
{
    use HasFactory;

    public const PRAYERS = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

    public const LABELS = [
        'fajr' => 'الفجر',
        'dhuhr' => 'الظهر',
        'asr' => 'العصر',
        'maghrib' => 'المغرب',
        'isha' => 'العشاء',
    ];

    protected $fillable = ['user_id', 'date', 'fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];

    protected $casts = [
        'date' => 'date',
        'fajr' => PrayerState::class,
        'dhuhr' => PrayerState::class,
        'asr' => PrayerState::class,
        'maghrib' => PrayerState::class,
        'isha' => PrayerState::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /** Number of the 5 prayers done (prayed or on time). */
    public function doneCount(): int
    {
        return collect(self::PRAYERS)->filter(fn ($p) => $this->{$p}->isDone())->count();
    }

    public function onTimeCount(): int
    {
        return collect(self::PRAYERS)->filter(fn ($p) => $this->{$p}->isOnTime())->count();
    }

    public function jamaahCount(): int
    {
        return collect(self::PRAYERS)->filter(fn ($p) => $this->{$p} === PrayerState::Congregation)->count();
    }

    public function isComplete(): bool
    {
        return $this->doneCount() === 5;
    }
}
