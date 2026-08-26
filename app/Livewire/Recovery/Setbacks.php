<?php

namespace App\Livewire\Recovery;

use App\Models\Recovery;
use App\Models\RecoveryLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * "الانتكاسات" — every relapse day in one place, linked cleanly to Recovery periods
 * (like "الأسبوع الأول", "الأسبوع الثاني") and clear timeframes.
 */
#[Layout('layouts.app')]
class Setbacks extends Component
{
    use WithPagination;

    #[Url]
    public ?int $recoveryId = null; // Filter by specific recovery period

    #[Url]
    public ?string $selectedWeek = 'all'; // Format: 'week-X', '7days', '30days', 'this_month', or 'all'

    public function mount(?int $recoveryId = null): void
    {
        if ($recoveryId) {
            $this->recoveryId = $recoveryId;
        }
    }

    public function updatedRecoveryId(): void
    {
        $this->selectedWeek = 'all';
        $this->resetPage();
    }

    public function updatingSelectedWeek(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $user = Auth::user();

        // Get all user recoveries for the dropdown selector
        $recoveries = Recovery::ownedBy($user)
            ->orderByDesc('start_date')
            ->get();

        // Base query for setbacks
        $query = RecoveryLog::query()
            ->whereHas('recovery', fn ($q) => $q->where('user_id', $user->id))
            ->where('is_setback', true)
            ->with('recovery:id,title');

        $availableWeeks = collect();
        $selectedRecovery = null;

        if ($this->recoveryId) {
            $query->where('recovery_id', $this->recoveryId);
            $selectedRecovery = $recoveries->firstWhere('id', $this->recoveryId);

            if ($selectedRecovery) {
                $recoveryStartDate = $selectedRecovery->start_date;
                $recoveryEndDate = $selectedRecovery->end_date ?? Carbon::now();

                $totalDays = (int) $recoveryStartDate->diffInDays($recoveryEndDate) + 1;

                // Only create sub-blocks if period is longer than 14 days,
                // computed from the recovery's own start date (not calendar weeks).
                if ($totalDays > 14) {
                    $blockStart = $recoveryStartDate->copy();
                    $weekNumber = 1;

                    while ($blockStart <= $recoveryEndDate) {
                        $blockEnd = $blockStart->copy()->addDays(6);
                        if ($blockEnd > $recoveryEndDate) {
                            $blockEnd = $recoveryEndDate->copy();
                        }

                        $setbacksInWeek = RecoveryLog::query()
                            ->where('recovery_id', $this->recoveryId)
                            ->where('is_setback', true)
                            ->whereBetween('date', [$blockStart, $blockEnd])
                            ->count();

                        $availableWeeks->push((object) [
                            'number' => $weekNumber,
                            'start_date' => $blockStart->copy(),
                            'end_date' => $blockEnd->copy(),
                            'count' => $setbacksInWeek,
                            'key' => 'week-'.$weekNumber,
                        ]);

                        $blockStart->addDays(7);
                        $weekNumber++;
                    }
                }

                if ($this->selectedWeek && $this->selectedWeek !== 'all') {
                    if ($this->selectedWeek === 'current') {
                        $latestWeek = $availableWeeks->last();
                        if ($latestWeek) {
                            $query->whereBetween('date', [$latestWeek->start_date, $latestWeek->end_date]);
                        }
                    } elseif (str_starts_with($this->selectedWeek, 'week-')) {
                        $weekNum = (int) str_replace('week-', '', $this->selectedWeek);
                        $selectedWeekData = $availableWeeks->firstWhere('number', $weekNum);
                        if ($selectedWeekData) {
                            $query->whereBetween('date', [$selectedWeekData->start_date, $selectedWeekData->end_date]);
                        }
                    }
                }
            }
        } else {
            // Global view across all recoveries using human timeframes
            if ($this->selectedWeek && $this->selectedWeek !== 'all') {
                if ($this->selectedWeek === '7days') {
                    $query->where('date', '>=', Carbon::now()->subDays(7)->toDateString());
                } elseif ($this->selectedWeek === '30days') {
                    $query->where('date', '>=', Carbon::now()->subDays(30)->toDateString());
                } elseif ($this->selectedWeek === 'this_month') {
                    $query->whereBetween('date', [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()]);
                }
            }
        }

        $setbacks = $query->orderByDesc('date')->paginate(12);

        return view('livewire.recovery.setbacks', [
            'setbacks' => $setbacks,
            'recoveries' => $recoveries,
            'availableWeeks' => $availableWeeks,
            'recovery' => $selectedRecovery,
            // Totals across ALL recovery periods, regardless of status — unaffected by the filters above.
            'totalRecoveryDays' => $recoveries->sum(fn (Recovery $r) => $r->periodTotalDays()),
            'totalCleanDays' => $recoveries->sum(fn (Recovery $r) => $r->cleanDaysCount()),
            'totalSetbackDays' => $recoveries->sum(fn (Recovery $r) => $r->setbackCount()),
        ]);
    }
}
