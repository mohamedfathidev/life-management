<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SharedChallenge extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'name', 'description', 'start_date', 'end_date', 'join_code', 'scoring'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'scoring' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'shared_challenge_user')->withTimestamps();
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ChallengeEntry::class);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Compute the points for a day's entry given this challenge's scoring config.
     *
     * @param  array<string, string>  $prayers   prayer key => state (jamaah/ontime/prayed/none)
     * @param  array<string, bool>  $extrasDone  extra key => done
     */
    public function computePoints(array $prayers, int $wirdPages, array $extrasDone): int
    {
        $scoring = $this->scoring;
        $points = 0;

        if ($scoring['prayer']['enabled'] ?? false) {
            foreach ($prayers as $state) {
                $points += (int) ($scoring['prayer']['points'][$state] ?? 0);
            }
        }

        if ($scoring['wird']['enabled'] ?? false) {
            $points += max(0, $wirdPages) * (int) ($scoring['wird']['points_per_page'] ?? 0);
        }

        foreach ($scoring['extras'] ?? [] as $extra) {
            if (! empty($extrasDone[$extra['key']])) {
                $points += (int) $extra['points'];
            }
        }

        return $points;
    }

    public function isJoinedBy(User $user): bool
    {
        return $this->participants()->whereKey($user->id)->exists();
    }

    public function isRunning(): bool
    {
        $today = Carbon::today();

        return $today->gte($this->start_date) && (! $this->end_date || $today->lte($this->end_date));
    }

    public function statusLabel(): string
    {
        $today = Carbon::today();
        if ($today->lt($this->start_date)) {
            return 'لسه ماابتداش';
        }
        if ($this->end_date && $today->gt($this->end_date)) {
            return 'انتهى';
        }

        return 'شغّال';
    }

    public static function generateCode(): string
    {
        do {
            $code = Str::upper(Str::random(6));
        } while (self::where('join_code', $code)->exists());

        return $code;
    }

    /** Default scoring configuration for a new challenge. */
    public static function defaultScoring(): array
    {
        return [
            'prayer' => ['enabled' => true, 'points' => ['jamaah' => 5, 'ontime' => 3, 'prayed' => 1, 'none' => 0]],
            'wird' => ['enabled' => true, 'points_per_page' => 1],
            'extras' => [
                ['key' => 'qiyam', 'label' => 'قيام الليل', 'points' => 5],
                ['key' => 'nawafil', 'label' => 'النوافل', 'points' => 2],
                ['key' => 'athkar', 'label' => 'أذكار الصباح والمساء', 'points' => 2],
            ],
        ];
    }
}
