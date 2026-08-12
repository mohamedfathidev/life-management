<?php

namespace App\Livewire\Appointments;

use App\Enums\AppointmentType;
use App\Livewire\Forms\AppointmentForm;
use App\Models\Appointment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public AppointmentForm $form;

    public bool $open = false;

    /** Currently-viewed month (Y-m). */
    #[Url]
    public string $month = '';

    public function mount(): void
    {
        if ($this->month === '') {
            $this->month = Carbon::today()->format('Y-m');
        }
    }

    public function goToMonth(int $offset): void
    {
        $this->month = Carbon::parse($this->month.'-01')->addMonths($offset)->format('Y-m');
    }

    public function goToday(): void
    {
        $this->month = Carbon::today()->format('Y-m');
    }

    public function openCreate(?string $date = null): void
    {
        $this->resetValidation();
        $this->form->prepareForCreate($date);
        $this->open = true;
    }

    public function editAppointment(int $id): void
    {
        $appointment = Appointment::ownedBy(Auth::user())->findOrFail($id);
        $this->resetValidation();
        $this->form->setAppointment($appointment);
        $this->open = true;
    }

    public function deleteAppointment(int $id): void
    {
        Appointment::ownedBy(Auth::user())->where('id', $id)->delete();
    }

    /** Mark an appointment as done (or undo) so it leaves the upcoming list. */
    public function toggleDone(int $id): void
    {
        $appointment = Appointment::ownedBy(Auth::user())->findOrFail($id);
        $appointment->update(['is_done' => ! $appointment->is_done]);
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
        $monthDate = Carbon::parse($this->month.'-01');
        $gridStart = $monthDate->copy()->startOfMonth()->startOfWeek(Carbon::SATURDAY);
        $gridEnd = $monthDate->copy()->endOfMonth()->endOfWeek(Carbon::FRIDAY);

        // Events in the visible grid, grouped by date.
        $events = Appointment::query()
            ->ownedBy(Auth::user())
            ->whereBetween('date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->orderBy('time')
            ->get()
            ->groupBy(fn (Appointment $a) => $a->date->toDateString());

        // Build weeks (each 7 cells).
        $weeks = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $key = $cursor->toDateString();
                $week[] = [
                    'date' => $key,
                    'day' => $cursor->day,
                    'inMonth' => $cursor->month === $monthDate->month,
                    'isToday' => $cursor->isToday(),
                    'events' => $events->get($key, collect()),
                ];
                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        // Upcoming = not done, and not past its date/time yet.
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');

        $upcoming = Appointment::query()
            ->ownedBy(Auth::user())
            ->where('is_done', false)
            ->where(function ($q) use ($today, $nowTime) {
                $q->whereDate('date', '>', $today)
                    ->orWhere(fn ($q2) => $q2->whereDate('date', $today)
                        ->where(fn ($q3) => $q3->whereNull('time')->orWhere('time', '>=', $nowTime)));
            })
            ->orderBy('date')->orderBy('time')->limit(20)->get();

        // Past / done — recent history.
        $past = Appointment::query()
            ->ownedBy(Auth::user())
            ->where(function ($q) use ($today, $nowTime) {
                $q->where('is_done', true)
                    ->orWhereDate('date', '<', $today)
                    ->orWhere(fn ($q2) => $q2->whereDate('date', $today)->whereNotNull('time')->where('time', '<', $nowTime));
            })
            ->orderByDesc('date')->orderByDesc('time')->limit(15)->get();

        return view('livewire.appointments.index', [
            'monthLabel' => $monthDate->translatedFormat('F Y'),
            'weekDays' => ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
            'weeks' => $weeks,
            'upcoming' => $upcoming,
            'past' => $past,
            'types' => AppointmentType::cases(),
        ]);
    }
}
