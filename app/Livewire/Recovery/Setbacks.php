<?php

namespace App\Livewire\Recovery;

use App\Models\NightCheck;
use App\Models\RecoveryLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "الانتكاسات" — every relapse day in one place: time, causes (trigger + notes),
 * and whether the user stayed up / fed their mind that day.
 */
#[Layout('layouts.app')]
class Setbacks extends Component
{
    public function render(): View
    {
        $user = Auth::user();

        $setbacks = RecoveryLog::query()
            ->whereHas('recovery', fn ($q) => $q->where('user_id', $user->id))
            ->where('is_setback', true)
            ->with('recovery:id,title')
            ->orderByDesc('date')->get();

        $nights = NightCheck::query()
            ->where('user_id', $user->id)
            ->whereIn('date', $setbacks->pluck('date')->map->toDateString()->all())
            ->get()
            ->keyBy(fn (NightCheck $n) => $n->date->toDateString());

        return view('livewire.recovery.setbacks', [
            'setbacks' => $setbacks,
            'nights' => $nights,
        ]);
    }
}
