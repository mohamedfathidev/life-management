<?php

namespace App\Livewire\Forms;

use App\Enums\AppointmentType;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class AppointmentForm extends Form
{
    public ?Appointment $appointment = null;

    public string $title = '';
    public string $type = 'other';
    public string $date = '';
    public ?string $time = null;
    public ?string $location = null;
    public ?string $note = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(AppointmentType::class)],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['title' => 'العنوان', 'type' => 'النوع', 'date' => 'التاريخ', 'time' => 'الوقت'];
    }

    public function setAppointment(Appointment $appointment): void
    {
        $this->appointment = $appointment;
        $this->title = $appointment->title;
        $this->type = $appointment->type->value;
        $this->date = $appointment->date->toDateString();
        $this->time = $appointment->time ? substr($appointment->time, 0, 5) : null;
        $this->location = $appointment->location;
        $this->note = $appointment->note;
    }

    public function prepareForCreate(?string $date = null): void
    {
        $this->reset();
        $this->type = 'other';
        $this->date = $date ?: Carbon::today()->toDateString();
    }

    public function persist(int $userId): Appointment
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->appointment) {
            $this->appointment->update($data);

            return $this->appointment;
        }

        return Appointment::create($data);
    }
}
