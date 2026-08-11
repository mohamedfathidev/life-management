<?php

namespace App\Livewire\Dashboard;

use App\Models\DailyLog;
use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Overview extends Component
{
    /** Refresh stats when a log or goal changes anywhere in the app. */
    #[On('log-saved')]
    #[On('goal-saved')]
    public function refresh(): void
    {
        // Presence triggers re-render; render() recomputes stats.
    }

    public function render(): View
    {
        $service = DashboardService::for(Auth::user());

        $todaysLogs = DailyLog::query()
            ->ownedBy(Auth::user())
            ->forDate(now())
            ->with('goal')
            ->latest()
            ->get();

        return view('livewire.dashboard.overview', [
            'activeGoals' => $service->activeGoalsCount(),
            'totalGoals' => $service->totalGoalsCount(),
            'logsToday' => $service->logsTodayCount(),
            'moodTrend' => $service->moodTrend(),
            'deadlines' => $service->upcomingDeadlines(),
            'todaysLogs' => $todaysLogs,
        ]);
    }
}
