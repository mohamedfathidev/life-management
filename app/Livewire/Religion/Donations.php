<?php

namespace App\Livewire\Religion;

use App\Livewire\Forms\DonationForm;
use App\Models\Donation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Donations extends Component
{
    public DonationForm $form;

    public bool $open = false;

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    public function editDonation(int $id): void
    {
        $donation = Donation::ownedBy(Auth::user())->findOrFail($id);
        $this->resetValidation();
        $this->form->setDonation($donation);
        $this->open = true;
    }

    public function deleteDonation(int $id): void
    {
        // Instance delete (not mass delete) so the mirrored wallet expense is removed too.
        Donation::ownedBy(Auth::user())->find($id)?->delete();
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
        $donations = Donation::query()->ownedBy(Auth::user())->latest('date')->latest()->get();

        $monthStart = Carbon::today()->startOfMonth();
        $thisMonth = $donations->filter(fn (Donation $d) => $d->date->gte($monthStart));

        return view('livewire.religion.donations', [
            'donations' => $donations,
            'total' => (float) $donations->sum('amount'),
            'monthTotal' => (float) $thisMonth->sum('amount'),
        ]);
    }
}
