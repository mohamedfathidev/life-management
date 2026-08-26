<?php

namespace App\Livewire\Recovery;

use App\Enums\MentalNutritionSourceType;
use App\Models\MentalNutritionLog;
use App\Services\MentalNutritionPoolService;
use App\Support\MentalNutritionItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MentalNutrition extends Component
{
    public ?string $reflection = null;

    /** Optional manual override of the suggested item ("موضوع تاني"), as "type:id". */
    public ?string $suggestedKey = null;

    /** Pick a different item than the current suggestion, from any source. */
    public function shuffleSuggestion(): void
    {
        $pool = $this->pool()->pool();
        $others = $pool->reject(fn (MentalNutritionItem $item) => $item->key() === $this->suggestedKey);

        $this->suggestedKey = ($others->isNotEmpty() ? $others : $pool)->random()?->key();
    }

    /** Mark today's item as consumed (read + reflected on). */
    public function markConsumed(string $type, int $id): void
    {
        $sourceType = MentalNutritionSourceType::from($type);
        $item = $this->pool()->resolve($sourceType, $id);

        if (! $item) {
            return;
        }

        MentalNutritionLog::updateOrCreate(
            ['user_id' => Auth::id(), 'date' => Carbon::today()->toDateString()],
            ['source_type' => $sourceType, 'source_id' => $id, 'reflection' => $this->reflection],
        );

        $this->reflection = null;
    }

    private function pool(): MentalNutritionPoolService
    {
        return new MentalNutritionPoolService(Auth::user());
    }

    /**
     * The item to show today: an explicit override, else the least-recently
     * shown item across every source, tie-broken by a per-day pseudo-random
     * order so untouched items rotate across sources instead of always
     * favoring whichever tab happens to sort first.
     */
    private function suggestItem(Collection $pool): ?MentalNutritionItem
    {
        if ($this->suggestedKey) {
            $override = $pool->first(fn (MentalNutritionItem $item) => $item->key() === $this->suggestedKey);
            if ($override) {
                return $override;
            }
        }

        $lastShown = MentalNutritionLog::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('source_type')
            ->whereNotNull('source_id')
            ->selectRaw('source_type, source_id, MAX(date) as last_date')
            ->groupBy('source_type', 'source_id')
            ->get()
            ->keyBy(fn ($row) => $row->source_type->value.':'.$row->source_id)
            ->map(fn ($row) => $row->last_date);

        $daySeed = (int) Carbon::today()->format('Ymd');

        return $pool
            ->sortBy(fn (MentalNutritionItem $item) => [
                $lastShown[$item->key()] ?? '0000-00-00',
                crc32($item->key().$daySeed),
            ])
            ->first();
    }

    /** Current consecutive-day streak of mental-nutrition, ending today or yesterday. */
    private function currentStreak(): int
    {
        $dates = MentalNutritionLog::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();
        // Anchor to today if done, otherwise to yesterday (streak not yet broken).
        $cursor = $dates->contains($today->toDateString())
            ? $today->copy()
            : $today->copy()->subDay();

        $streak = 0;
        while ($dates->contains($cursor->toDateString())) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }

    public function render(): View
    {
        $pool = $this->pool()->pool();

        $todayLog = MentalNutritionLog::query()
            ->where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        $recent = MentalNutritionLog::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('date')
            ->limit(7)
            ->get();

        return view('livewire.recovery.mental-nutrition', [
            'hasItems' => $pool->isNotEmpty(),
            'todayLog' => $todayLog,
            'todayItem' => $todayLog && $todayLog->source_type ? $this->pool()->resolve($todayLog->source_type, $todayLog->source_id) : null,
            'suggested' => $todayLog ? null : $this->suggestItem($pool),
            'streak' => $this->currentStreak(),
            'recent' => $recent->map(fn (MentalNutritionLog $log) => [
                'log' => $log,
                'item' => $log->source_type ? $this->pool()->resolve($log->source_type, $log->source_id) : null,
            ]),
        ]);
    }
}
