<?php

namespace App\Livewire\DailyLogs;

use App\Enums\ModuleType;
use App\Livewire\Forms\DailyLogForm;
use App\Models\DailyLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageLog extends Component
{
    public DailyLogForm $form;

    public bool $open = false;

    #[On('create-log')]
    public function openForCreate(?int $goalId = null, string $moduleType = 'general'): void
    {
        $this->form->reset();
        $this->resetValidation();
        $this->form->goal_id = $goalId;
        $this->form->module_type = ModuleType::tryFrom($moduleType)?->value ?? ModuleType::General->value;
        $this->form->date = Carbon::today()->toDateString();
        $this->open = true;
    }

    #[On('edit-log')]
    public function openForEdit(DailyLog $log): void
    {
        $this->authorize('update', $log);
        $this->resetValidation();
        $this->form->setLog($log);
        $this->open = true;
    }

    #[On('delete-log')]
    public function delete(DailyLog $log): void
    {
        $this->authorize('delete', $log);
        $log->delete();

        $this->dispatch('log-saved');
    }

    public function save(): void
    {
        if ($this->form->log) {
            $this->authorize('update', $this->form->log);
        }

        $this->form->persist(Auth::id());

        $this->open = false;
        $this->dispatch('log-saved');
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.daily-logs.manage-log', [
            'modules' => ModuleType::cases(),
        ]);
    }
}
