<?php

namespace App\Livewire\Dashboard;

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

        return view('livewire.dashboard.overview', [
            'activeGoals' => $service->activeGoalsCount(),
            'totalGoals' => $service->totalGoalsCount(),
            'moodTrend' => $service->moodTrendForWeek(),
            'deadlines' => $service->upcomingDeadlines(),
            // today at a glance
            'todayPlan' => $service->todayPlan(),
            'prayers' => $service->prayersToday(),
            'habits' => $service->habitsToday(),
            'challenges' => $service->challengesToday(),
            'recoveries' => $service->activeRecoveries(),
            'appointments' => $service->upcomingAppointments(),
        ]);
    }
}
