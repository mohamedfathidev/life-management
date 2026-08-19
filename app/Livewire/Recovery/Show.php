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
    public ?string $selectedWeek = null; // Format: 'YYYY-WW' or 'all'

    public function mount(Recovery $recovery): void
    {
        $this->authorize('view', $recovery);
        $this->recovery = $recovery;
        // Default to current week
        $this->selectedWeek = 'current';
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
        // Calculate weeks for this recovery based on start_date
        $recoveryStartDate = $this->recovery->start_date;
        $recoveryEndDate = $this->recovery->end_date ?? Carbon::now();
        
        // Generate all weeks in the recovery period
        $allWeeks = collect();
        $currentWeekStart = $recoveryStartDate->copy()->startOfWeek();
        $weekNumber = 1;
        
        while ($currentWeekStart <= $recoveryEndDate) {
            $weekEnd = $currentWeekStart->copy()->endOfWeek();
            if ($weekEnd > $recoveryEndDate) {
                $weekEnd = $recoveryEndDate;
            }
            
            // Get logs count for this week
            $logsInWeek = $this->recovery->logs()
                ->whereBetween('date', [$currentWeekStart, $weekEnd])
                ->get();
            
            $allWeeks->push((object)[
                'number' => $weekNumber,
                'start_date' => $currentWeekStart->copy(),
                'end_date' => $weekEnd->copy(),
                'year' => $currentWeekStart->year,
                'week' => $currentWeekStart->week,
                'count' => $logsInWeek->count(),
                'setback_count' => $logsInWeek->where('is_setback', true)->count(),
                'key' => 'week-' . $weekNumber,
            ]);
            
            $currentWeekStart->addWeek();
            $weekNumber++;
        }

        // Base query for logs
        $logsQuery = $this->recovery->logs();

        // Filter by week if selected
        if ($this->selectedWeek && $this->selectedWeek !== 'all') {
            if ($this->selectedWeek === 'current') {
                // Current week in the recovery (the latest week)
                $latestWeek = $allWeeks->last();
                if ($latestWeek) {
                    $logsQuery->whereBetween('date', [$latestWeek->start_date, $latestWeek->end_date]);
                }
            } else {
                // Specific week by number (format: week-X)
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
