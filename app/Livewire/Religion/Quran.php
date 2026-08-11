<?php

namespace App\Livewire\Religion;

use App\Livewire\Forms\QuranLogForm;
use App\Models\QuranLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Quran extends Component
{
    public QuranLogForm $form;

    public bool $open = false;

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    public function editLog(int $logId): void
    {
        $log = QuranLog::ownedBy(Auth::user())->findOrFail($logId);
        $this->resetValidation();
        $this->form->setLog($log);
        $this->open = true;
    }

    public function deleteLog(int $logId): void
    {
        QuranLog::ownedBy(Auth::user())->where('id', $logId)->delete();
    }

    public function save(): void
    {
        $this->form->persist(Auth::id());
        $this->open = false;
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        $logs = QuranLog::query()->ownedBy(Auth::user())->latest('date')->latest()->get();

        $totalPages = (int) $logs->sum('pages');
        $khatmahs = intdiv($totalPages, QuranLog::MUSHAF_PAGES);
        $currentPages = $totalPages % QuranLog::MUSHAF_PAGES;

        return view('livewire.religion.quran', [
            'logs' => $logs,
            'totalPages' => $totalPages,
            'khatmahs' => $khatmahs,
            'currentPages' => $currentPages,
            'khatmahPercent' => (int) round($currentPages / QuranLog::MUSHAF_PAGES * 100),
            'mushafPages' => QuranLog::MUSHAF_PAGES,
        ]);
    }
}
