<?php

namespace App\Livewire\Recovery;

use App\Models\RecoveryBrainComparison;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * "الدماغ الإدماني ضد دماغي الطبيعية" — same point, two brains: what the
 * addicted brain wants (red) next to what the user's healthy brain actually
 * wants (green), side by side.
 */
#[Layout('layouts.app')]
class BrainComparisons extends Component
{
    use WithPagination;

    public ?int $editingId = null;

    public string $point = '';

    public string $addictiveText = '';

    public string $normalText = '';

    public function save(): void
    {
        $data = $this->validate([
            'point' => ['required', 'string', 'max:255'],
            'addictiveText' => ['required', 'string', 'max:1000'],
            'normalText' => ['required', 'string', 'max:1000'],
        ], attributes: [
            'point' => 'النقطة',
            'addictiveText' => 'الدماغ الإدماني',
            'normalText' => 'دماغي الطبيعية',
        ]);

        if ($this->editingId) {
            $comparison = RecoveryBrainComparison::ownedBy(Auth::user())->findOrFail($this->editingId);
            $comparison->update([
                'point' => $data['point'],
                'addictive_text' => $data['addictiveText'],
                'normal_text' => $data['normalText'],
            ]);
        } else {
            RecoveryBrainComparison::create([
                'user_id' => Auth::id(),
                'point' => $data['point'],
                'addictive_text' => $data['addictiveText'],
                'normal_text' => $data['normalText'],
                'position' => (int) RecoveryBrainComparison::ownedBy(Auth::user())->max('position') + 1,
            ]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $comparison = RecoveryBrainComparison::ownedBy(Auth::user())->findOrFail($id);

        $this->editingId = $comparison->id;
        $this->point = $comparison->point;
        $this->addictiveText = $comparison->addictive_text;
        $this->normalText = $comparison->normal_text;
    }

    public function delete(int $id): void
    {
        RecoveryBrainComparison::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'point', 'addictiveText', 'normalText']);
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.recovery.brain-comparisons', [
            'comparisons' => RecoveryBrainComparison::ownedBy(Auth::user())
                ->orderBy('position')->latest()->paginate(12),
        ]);
    }
}
