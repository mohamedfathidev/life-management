<?php

namespace App\Models;

use App\Enums\DreamStatus;
use App\Enums\DurationUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Dream extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'why', 'from_point', 'to_point',
        'duration_value', 'duration_unit', 'target_date', 'status', 'color', 'position',
    ];

    protected $casts = [
        'duration_value' => 'integer',
        'duration_unit' => DurationUnit::class,
        'target_date' => 'date',
        'status' => DreamStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paths(): HasMany
    {
        return $this->hasMany(DreamPath::class)->orderBy('position')->orderBy('id');
    }

    public function milestones(): HasManyThrough
    {
        return $this->hasManyThrough(DreamMilestone::class, DreamPath::class);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function durationLabel(): ?string
    {
        if (! $this->duration_value) {
            return null;
        }

        return $this->duration_value.' '.$this->duration_unit->label();
    }

    /** @return array{0:int,1:int,2:int} RGB of the dream color (or the default teal). */
    private function rgb(?string $hex = null): array
    {
        $hex = ltrim($hex ?? ($this->color ?: '#3F7D7A'), '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            (int) hexdec(substr($hex, 0, 2)),
            (int) hexdec(substr($hex, 2, 2)),
            (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    /** A darker, richer shade of the dream color — used for the destination node. */
    public function darkerColor(float $factor = 0.62): string
    {
        [$r, $g, $b] = $this->rgb();

        return sprintf('#%02x%02x%02x', (int) ($r * $factor), (int) ($g * $factor), (int) ($b * $factor));
    }

    /** Readable text color (dark or white) for text placed on the given background. */
    public function contrastText(?string $hex = null): string
    {
        [$r, $g, $b] = $this->rgb($hex);
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        return $luminance > 0.6 ? '#1f2937' : '#ffffff';
    }

    /** Overall progress 0–100 across all milestones in all paths. */
    public function progressPercent(): int
    {
        $total = $this->milestones()->count();

        if ($total === 0) {
            return 0;
        }

        $done = $this->milestones()->where('is_done', true)->count();

        return (int) round($done / $total * 100);
    }
}
