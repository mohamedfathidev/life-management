<?php

namespace App\Models;

use App\Enums\AppointmentType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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

    /**
     * A "Add to Google Calendar" link that opens Google Calendar pre-filled with
     * this appointment (timed → 1h default; otherwise all-day).
     */
    public function googleCalendarUrl(): string
    {
        if ($this->time) {
            $start = Carbon::parse($this->date->toDateString().' '.$this->time);
            $end = $start->copy()->addHour();
            $dates = $start->format('Ymd\THis').'/'.$end->format('Ymd\THis');
        } else {
            $start = $this->date->copy();
            $dates = $start->format('Ymd').'/'.$start->copy()->addDay()->format('Ymd');
        }

        $query = 'action=TEMPLATE'
            .'&text='.rawurlencode($this->title)
            .'&dates='.$dates
            .'&details='.rawurlencode((string) $this->note)
            .'&location='.rawurlencode((string) $this->location);

        return 'https://calendar.google.com/calendar/render?'.$query;
    }
}
