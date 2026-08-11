<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Week extends Model
{
    use HasFactory;

    /** The week starts on Saturday (per user preference). */
    public const START_DAY = Carbon::SATURDAY;

    protected $fillable = ['user_id', 'start_date', 'end_date', 'note'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(Day::class)->orderBy('date');
    }

    /**
     * The [start, end] boundaries (Sat → Fri) of the week containing $date.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function boundariesFor(Carbon $date): array
    {
        $start = $date->copy()->startOfWeek(self::START_DAY);

        return [$start, $start->copy()->addDays(6)];
    }
}
