<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryHardMoment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageHardMoment extends Component
{
    public ?RecoveryHardMoment $moment = null;

    public bool $open = false;

    public string $title = '';

    public ?string $description = null;

    public ?string $plan = null;

    #[On('edit-hard-moment')]
    public function openForEdit(int $moment): void
    {
        $this->moment = RecoveryHardMoment::ownedBy(Auth::user())->findOrFail($moment);
        $this->title = $this->moment->title;
        $this->description = $this->moment->description;
        $this->plan = $this->moment->plan;
        $this->resetValidation();
        $this->open = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'plan' => ['nullable', 'string'],
        ], attributes: [
            'title' => 'اللحظة',
            'description' => 'الوصف',
            'plan' => 'خطة المواجهة',
        ]);

        $this->moment->update($data);

        $this->open = false;
        $this->dispatch('hard-moment-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->reset('moment', 'title', 'description', 'plan');
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.manage-hard-moment');
    }
}
