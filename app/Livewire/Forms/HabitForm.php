<?php

namespace App\Livewire\Forms;

use App\Enums\HabitType;
use App\Models\Habit;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class HabitForm extends Form
{
    public ?Habit $habit = null;

    public ?int $goal_id = null;
    public string $title = '';
    public string $type = 'recurring';
    public string $start_date = '';
    public ?string $end_date = null;
    public string $color = '#3F7D7A';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'goal_id' => ['nullable', 'integer', 'exists:goals,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(HabitType::class)],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'required_if:type,intermittent', 'date', 'after_or_equal:start_date'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'العنوان',
            'type' => 'النوع',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'color' => 'اللون',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'end_date.required_if' => 'العادة المتقطعة لازم يكون ليها تاريخ نهاية.',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
        ];
    }

    public function setHabit(Habit $habit): void
    {
        $this->habit = $habit;
        $this->goal_id = $habit->goal_id;
        $this->title = $habit->title;
        $this->type = $habit->type->value;
        $this->start_date = $habit->start_date->toDateString();
        $this->end_date = $habit->end_date?->toDateString();
        $this->color = $habit->color;
    }

    public function prepareForCreate(): void
    {
        $this->reset();
        $this->type = 'recurring';
        $this->start_date = Carbon::today()->toDateString();
        $this->color = '#3F7D7A';
    }

    public function persist(int $userId): Habit
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        // Recurring habits have no end date.
        if ($data['type'] !== HabitType::Intermittent->value) {
            $data['end_date'] = null;
        }

        if ($this->habit) {
            $this->habit->update($data);

            return $this->habit;
        }

        return Habit::create($data);
    }
}
