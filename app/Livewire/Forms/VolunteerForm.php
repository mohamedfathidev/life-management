<?php

namespace App\Livewire\Forms;

use App\Enums\ScholarshipStage;
use App\Models\VolunteerActivity;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class VolunteerForm extends Form
{
    public ?VolunteerActivity $activity = null;

    public string $title = '';
    public ?string $organization = null;
    public ?string $applied_via = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?int $hours = null;
    public ?string $description = null;
    public string $stage = 'preparing';
    public ?string $submitted_on = null;
    public ?string $decided_on = null;
    public ?string $rejection_reason = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'applied_via' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:65000'],
            'description' => ['nullable', 'string', 'max:10000'],
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
            'title' => 'النشاط',
            'organization' => 'الجهة',
            'applied_via' => 'قدّمت من خلال',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'hours' => 'الساعات',
            'stage' => 'المرحلة',
        ];
    }

    public function setActivity(VolunteerActivity $activity): void
    {
        $this->activity = $activity;
        $this->title = $activity->title;
        $this->organization = $activity->organization;
        $this->applied_via = $activity->applied_via;
        $this->start_date = $activity->start_date?->toDateString();
        $this->end_date = $activity->end_date?->toDateString();
        $this->hours = $activity->hours;
        $this->description = $activity->description;
        $this->stage = $activity->stage->value;
        $this->submitted_on = $activity->submitted_on?->toDateString();
        $this->decided_on = $activity->decided_on?->toDateString();
        $this->rejection_reason = $activity->rejection_reason;
    }

    public function persist(int $userId): VolunteerActivity
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->activity) {
            $this->activity->update($data);

            return $this->activity;
        }

        return VolunteerActivity::create($data);
    }
}
