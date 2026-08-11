<?php

namespace App\Livewire\MarketStudy;

use App\Models\CareerSetting;
use App\Models\StudyTrack;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $motto = '';
    public bool $editingMotto = false;

    public function mount(): void
    {
        $this->motto = (string) optional($this->setting())->study_motto;
    }

    private function setting(): ?CareerSetting
    {
        return CareerSetting::firstWhere('user_id', Auth::id());
    }

    public function saveMotto(): void
    {
        CareerSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            ['study_motto' => $this->motto ?: null],
        );
        $this->editingMotto = false;
    }

    #[On('track-saved')]
    public function refreshList(): void
    {
        //
    }

    public function render(): View
    {
        $tracks = StudyTrack::query()
            ->ownedBy(Auth::user())
            ->orderBy('is_completed')
            ->latest()
            ->get();

        return view('livewire.market-study.index', [
            'tracks' => $tracks,
        ]);
    }
}
