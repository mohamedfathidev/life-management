<?php

namespace App\Livewire\Recovery;

use App\Enums\RecoveryStatus;
use App\Models\Recovery;
use App\Models\RecoveryDamage;
use App\Models\RecoveryDream;
use App\Models\RecoveryHardMoment;
use App\Models\RecoveryLog;
use App\Models\RecoveryMistake;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "قبل الوقوع تذكر" — a fork-in-the-road reflection page meant to be opened
 * in the moment of temptation, not browsed casually. Both paths are built
 * from the user's own recorded history (triggers, damages, coping plans,
 * dreams) wherever it exists, so the warning is personal rather than generic.
 */
#[Layout('layouts.app')]
class Remember extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        $recoveries = Recovery::ownedBy($user)->get();
        $recoveryIds = $recoveries->pluck('id');
        $active = $recoveries->firstWhere('status', RecoveryStatus::Active);

        $recentTriggers = RecoveryLog::query()
            ->whereIn('recovery_id', $recoveryIds)
            ->where('is_setback', true)
            ->whereNotNull('trigger_note')
            ->latest('date')
            ->limit(15)
            ->pluck('trigger_note')
            ->filter(fn ($note) => trim((string) $note) !== '')
            ->unique()
            ->take(4)
            ->values();

        $hardMoments = RecoveryHardMoment::ownedBy($user)->latest()->limit(4)->get();

        $damages = RecoveryDamage::query()->ownedBy($user)->main()->orderByDesc('degree')->limit(4)->get();
        $mistakes = RecoveryMistake::ownedBy($user)->orderByDesc('weight')->limit(3)->get();
        $setbackCount = $recoveries->sum(fn (Recovery $r) => $r->setbackCount());

        $protectionActions = RecoveryLog::query()
            ->whereIn('recovery_id', $recoveryIds)
            ->where('is_setback', false)
            ->whereNotNull('protection_actions')
            ->latest('date')
            ->limit(15)
            ->pluck('protection_actions')
            ->flatten()
            ->filter(fn ($action) => trim((string) $action) !== '')
            ->unique()
            ->take(4)
            ->values();

        $copingPlans = RecoveryHardMoment::ownedBy($user)
            ->latest()
            ->limit(10)
            ->get()
            ->filter(fn (RecoveryHardMoment $m) => filled($m->plan))
            ->take(3)
            ->values();

        $dreams = RecoveryDream::ownedBy($user)->where('is_achieved', false)->latest()->limit(4)->get();
        $currentStreak = $active?->currentStreakDays() ?? 0;
        $bestStreak = (int) $recoveries->max(fn (Recovery $r) => $r->bestStreakDays());
        $cleanDays = $recoveries->sum(fn (Recovery $r) => $r->cleanDaysCount());

        return view('livewire.recovery.remember', [
            'recentTriggers' => $recentTriggers,
            'hardMoments' => $hardMoments,
            'damages' => $damages,
            'mistakes' => $mistakes,
            'setbackCount' => $setbackCount,
            'protectionActions' => $protectionActions,
            'copingPlans' => $copingPlans,
            'dreams' => $dreams,
            'currentStreak' => $currentStreak,
            'bestStreak' => $bestStreak,
            'cleanDays' => $cleanDays,
        ]);
    }
}
