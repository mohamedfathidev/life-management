<?php

namespace App\Livewire\Forms;

use App\Enums\RecoveryStatus;
use App\Models\Recovery;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Carbon;
use Livewire\Form;

class RecoveryForm extends Form
{
    public ?Recovery $recovery = null;

    public ?int $goal_id = null;
    public string $title = '';
    public ?string $description = null;
    public string $start_date = '';
    public ?string $end_date = null;
    public string $status = 'active';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'goal_id' => ['nullable', 'integer', 'exists:goals,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', new Enum(RecoveryStatus::class)],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'العنوان',
            'description' => 'الوصف',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'status' => 'الحالة',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
        ];
    }

    public function setRecovery(Recovery $recovery): void
    {
        $this->recovery = $recovery;
        $this->goal_id = $recovery->goal_id;
        $this->title = $recovery->title;
        $this->description = $recovery->description;
        $this->start_date = $recovery->start_date->toDateString();
        $this->end_date = $recovery->end_date?->toDateString();
        $this->status = $recovery->status->value;
    }

    public function persist(int $userId): Recovery
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->recovery) {
            $this->recovery->update($data);

            return $this->recovery;
        }

        return Recovery::create($data);
    }

    public function prepareForCreate(): void
    {
        $this->reset();
        $this->start_date = Carbon::today()->toDateString();
        $this->status = 'active';
    }
}
