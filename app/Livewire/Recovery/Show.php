<?php

namespace App\Livewire\Recovery;

use App\Models\Recovery;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Recovery $recovery;

    public ?string $selectedWeek = 'all';

    public function mount(Recovery $recovery): void
    {
        $this->authorize('view', $recovery);
        $this->recovery = $recovery;
        $this->selectedWeek = 'all';
    }

    public function setWeek(string $week): void
    {
        $this->selectedWeek = $week;
    }

    #[On('recovery-saved')]
    #[On('recovery-log-saved')]
    public function refreshRecovery(): void
    {
        $this->recovery->refresh();
    }

    public function editRecovery(): void
    {
        $this->dispatch('edit-recovery', recovery: $this->recovery->id);
    }

    public function addLog(bool $setback = false): void
    {
        $this->dispatch('create-recovery-log', recoveryId: $this->recovery->id, setback: $setback);
    }

    public function render(): View
    {
        $recoveryStartDate = $this->recovery->start_date;
        $recoveryEndDate = $this->recovery->end_date ?? Carbon::now();

        $totalDays = (int) $recoveryStartDate->diffInDays($recoveryEndDate) + 1;
        $allWeeks = collect();

        // Only create sub-blocks if period is longer than 14 days
        if ($totalDays > 14) {
            $blockStart = $recoveryStartDate->copy();
            $weekNumber = 1;

            while ($blockStart <= $recoveryEndDate) {
                $blockEnd = $blockStart->copy()->addDays(6);
                if ($blockEnd > $recoveryEndDate) {
                    $blockEnd = $recoveryEndDate->copy();
                }

                $logsInWeek = $this->recovery->logs()
                    ->whereBetween('date', [$blockStart, $blockEnd])
                    ->get();

                $allWeeks->push((object) [
                    'number' => $weekNumber,
                    'start_date' => $blockStart->copy(),
                    'end_date' => $blockEnd->copy(),
                    'count' => $logsInWeek->count(),
                    'setback_count' => $logsInWeek->where('is_setback', true)->count(),
                    'key' => 'week-'.$weekNumber,
                ]);

                $blockStart->addDays(7);
                $weekNumber++;
            }
        }

        // Base query for logs
        $logsQuery = $this->recovery->logs();

        // Filter by sub-week if selected
        if ($this->selectedWeek && $this->selectedWeek !== 'all' && $allWeeks->isNotEmpty()) {
            if ($this->selectedWeek === 'current') {
                $latestWeek = $allWeeks->last();
                if ($latestWeek) {
                    $logsQuery->whereBetween('date', [$latestWeek->start_date, $latestWeek->end_date]);
                }
            } else {
                $weekNum = (int) str_replace('week-', '', $this->selectedWeek);
                $selectedWeekData = $allWeeks->firstWhere('number', $weekNum);
                if ($selectedWeekData) {
                    $logsQuery->whereBetween('date', [$selectedWeekData->start_date, $selectedWeekData->end_date]);
                }
            }
        }

        $logs = $logsQuery->get();
        $remainingDays = $this->recovery->remainingDays();

        return view('livewire.recovery.show', [
            'logs' => $logs,
            'streakDays' => $this->recovery->currentStreakDays(),
            'streakSince' => $this->recovery->streakSince(),
            'remainingDays' => $remainingDays,
            'remainingDaysLabel' => $remainingDays !== null ? $remainingDays : '∞',
            'setbackCount' => $this->recovery->setbackCount(),
            'cleanDays' => $this->recovery->cleanDaysCount(),
            'availableWeeks' => $allWeeks,
        ]);
    }
}
