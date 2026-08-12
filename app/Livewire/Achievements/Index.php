<?php

namespace App\Livewire\Achievements;

use App\Services\AchievementService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render(): View
    {
        $state = AchievementService::sync(Auth::user());

        // Group items by their group label.
        $grouped = collect($state['items'])->groupBy(fn ($item) => $item['def']['group']);

        return view('livewire.achievements.index', [
            'grouped' => $grouped,
            'earnedCount' => $state['earnedCount'],
            'total' => $state['total'],
            'newlyUnlocked' => $state['new'],
        ]);
    }
}
