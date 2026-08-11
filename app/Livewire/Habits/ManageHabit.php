<?php

namespace App\Livewire\Habits;

use App\Enums\HabitType;
use App\Livewire\Forms\HabitForm;
use App\Models\Goal;
use App\Models\Habit;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageHabit extends Component
{
    public HabitForm $form;

    public bool $open = false;

    #[On('create-habit')]
    public function openForCreate(): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate();
        $this->open = true;
    }

    #[On('edit-habit')]
    public function openForEdit(Habit $habit): void
    {
        $this->authorize('update', $habit);
        $this->resetValidation();
        $this->form->setHabit($habit);
        $this->open = true;
    }

    public function save(): void
    {
        if ($this->form->habit) {
            $this->authorize('update', $this->form->habit);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('habit-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.habits.manage-habit', [
            'types' => HabitType::cases(),
            'goals' => Goal::query()->ownedBy(Auth::user())->orderBy('title')->get(['id', 'title', 'parent_id']),
        ]);
    }
}
