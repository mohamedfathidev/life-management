<?php

namespace App\Livewire\Career;

use App\Models\CareerDream;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "أحلام الكارير" — simple and self-contained: a dream, its status, and how
 * far it's gotten. No relation to the general Dream/paths/milestones system.
 */
#[Layout('layouts.app')]
class Dreams extends Component
{
    public ?int $editingId = null;

    public string $title = '';

    public string $status = 'dreaming';

    public ?string $progressNote = null;

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:dreaming,pursuing,achieved'],
            'progressNote' => ['nullable', 'string', 'max:1000'],
        ], attributes: [
            'title' => 'الحلم',
            'status' => 'الحالة',
            'progressNote' => 'وصلت لأيه',
        ]);

        if ($this->editingId) {
            $dream = CareerDream::ownedBy(Auth::user())->findOrFail($this->editingId);
            $dream->update([
                'title' => $data['title'],
                'status' => $data['status'],
                'progress_note' => $data['progressNote'],
            ]);
        } else {
            CareerDream::create([
                'user_id' => Auth::id(),
                'title' => $data['title'],
                'status' => $data['status'],
                'progress_note' => $data['progressNote'],
                'position' => (int) CareerDream::ownedBy(Auth::user())->max('position') + 1,
            ]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $dream = CareerDream::ownedBy(Auth::user())->findOrFail($id);

        $this->editingId = $dream->id;
        $this->title = $dream->title;
        $this->status = $dream->status->value;
        $this->progressNote = $dream->progress_note;
    }

    public function delete(int $id): void
    {
        CareerDream::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'progressNote']);
        $this->status = 'dreaming';
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.career.dreams', [
            'dreams' => CareerDream::ownedBy(Auth::user())->orderBy('position')->latest()->get(),
        ]);
    }
}
