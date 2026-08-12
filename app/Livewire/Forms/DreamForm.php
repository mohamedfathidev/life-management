<?php

namespace App\Livewire\Forms;

use App\Enums\DreamStatus;
use App\Enums\DurationUnit;
use App\Models\Dream;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class DreamForm extends Form
{
    public ?Dream $dream = null;

    public string $title = '';
    public ?string $description = null;
    public ?string $why = null;
    public ?string $from_point = null;
    public ?string $to_point = null;
    public ?int $duration_value = null;
    public string $duration_unit = 'years';
    public ?string $target_date = null;
    public string $status = 'dreaming';
    public string $color = '#3F7D7A';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'why' => ['nullable', 'string', 'max:10000'],
            'from_point' => ['nullable', 'string', 'max:255'],
            'to_point' => ['nullable', 'string', 'max:255'],
            'duration_value' => ['nullable', 'integer', 'min:1', 'max:600'],
            'duration_unit' => ['required', new Enum(DurationUnit::class)],
            'target_date' => ['nullable', 'date'],
            'status' => ['required', new Enum(DreamStatus::class)],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['title' => 'الحلم', 'from_point' => 'واقف فين', 'to_point' => 'عايز أوصل فين'];
    }

    public function setDream(Dream $dream): void
    {
        $this->dream = $dream;
        $this->title = $dream->title;
        $this->description = $dream->description;
        $this->why = $dream->why;
        $this->from_point = $dream->from_point;
        $this->to_point = $dream->to_point;
        $this->duration_value = $dream->duration_value;
        $this->duration_unit = $dream->duration_unit->value;
        $this->target_date = $dream->target_date?->toDateString();
        $this->status = $dream->status->value;
        $this->color = $dream->color;
    }

    public function prepareForCreate(): void
    {
        $this->reset();
        $this->duration_unit = 'years';
        $this->status = 'dreaming';
        $this->color = '#3F7D7A';
    }

    public function persist(int $userId): Dream
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->dream) {
            $this->dream->update($data);

            return $this->dream;
        }

        return Dream::create($data);
    }
}
