<?php

namespace App\Livewire\Forms;

use App\Enums\ScholarshipStage;
use App\Models\Scholarship;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ScholarshipForm extends Form
{
    public ?Scholarship $scholarship = null;

    public string $name = '';
    public ?string $institution = null;
    public ?string $apply_from = null;
    public ?string $apply_to = null;
    public ?string $requirements = null;
    public ?string $notes = null;
    public string $stage = 'preparing';
    public ?string $submitted_on = null;
    public ?string $decided_on = null;
    public ?string $rejection_reason = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'institution' => ['nullable', 'string', 'max:255'],
            'apply_from' => ['nullable', 'date'],
            'apply_to' => ['nullable', 'date', 'after_or_equal:apply_from'],
            'requirements' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'stage' => ['required', new Enum(ScholarshipStage::class)],
            'submitted_on' => ['nullable', 'date'],
            'decided_on' => ['nullable', 'date'],
            'rejection_reason' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'name' => 'اسم المنحة',
            'apply_from' => 'بداية التقديم',
            'apply_to' => 'نهاية التقديم',
            'stage' => 'المرحلة',
        ];
    }

    public function setScholarship(Scholarship $scholarship): void
    {
        $this->scholarship = $scholarship;
        $this->name = $scholarship->name;
        $this->institution = $scholarship->institution;
        $this->apply_from = $scholarship->apply_from?->toDateString();
        $this->apply_to = $scholarship->apply_to?->toDateString();
        $this->requirements = $scholarship->requirements;
        $this->notes = $scholarship->notes;
        $this->stage = $scholarship->stage->value;
        $this->submitted_on = $scholarship->submitted_on?->toDateString();
        $this->decided_on = $scholarship->decided_on?->toDateString();
        $this->rejection_reason = $scholarship->rejection_reason;
    }

    public function persist(int $userId): Scholarship
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->scholarship) {
            $this->scholarship->update($data);

            return $this->scholarship;
        }

        return Scholarship::create($data);
    }
}
