<?php

namespace App\Livewire\Notifications;

use App\Services\ReminderService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Bell extends Component
{
    public function render(): View
    {
        $reminders = ReminderService::for(Auth::user())->reminders();

        return view('livewire.notifications.bell', [
            'reminders' => $reminders,
            'count' => count($reminders),
        ]);
    }
}
