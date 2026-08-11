<?php

namespace App\Livewire\ComfortZone;

use App\Enums\ExperienceKind;
use App\Enums\ExperienceStatus;
use App\Livewire\Forms\ExperienceForm;
use App\Models\ComfortExperience;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public ExperienceForm $form;

    public bool $open = false;

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->form->reset();
        $this->open = true;
    }

    public function edit(int $id): void
    {
        $experience = ComfortExperience::ownedBy(Auth::user())->findOrFail($id);
        $this->resetValidation();
        $this->form->setExperience($experience);
        $this->open = true;
    }

    public function delete(int $id): void
    {
        ComfortExperience::ownedBy(Auth::user())->where('id', $id)->delete();
    }

    /** Quick mark-done (opens the editor to capture the reflection). */
    public function markDone(int $id): void
    {
        $experience = ComfortExperience::ownedBy(Auth::user())->findOrFail($id);
        $experience->update([
            'status' => ExperienceStatus::Done->value,
            'done_on' => $experience->done_on ?? now()->toDateString(),
        ]);

        $this->form->setExperience($experience->fresh());
        $this->open = true;
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
        $ongoing = ComfortExperience::query()->ownedBy(Auth::user())->ongoing()->latest()->get();
        $done = ComfortExperience::query()->ownedBy(Auth::user())->done()->latest('done_on')->latest()->get();

        return view('livewire.comfort-zone.index', [
            'ongoing' => $ongoing,
            'done' => $done,
            'doneCount' => $done->count(),
            'kinds' => ExperienceKind::cases(),
            'statuses' => ExperienceStatus::cases(),
        ]);
    }
}
