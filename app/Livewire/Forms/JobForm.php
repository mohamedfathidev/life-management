<?php

namespace App\Livewire\Forms;

use App\Enums\JobStage;
use App\Models\JobApplication;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class JobForm extends Form
{
    public ?JobApplication $job = null;

    public ?int $goal_id = null;
    public string $position = '';
    public string $company = '';
    public ?string $applied_via = null;
    public ?string $url = null;
    public ?string $description = null;
    public ?string $applied_on = null;
    public string $stage = 'wishlist';
    public ?string $rejection_reason = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'goal_id' => ['nullable', 'integer', 'exists:goals,id'],
            'position' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'applied_via' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:2000'],
            'description' => ['nullable', 'string', 'max:20000'],
            'applied_on' => ['nullable', 'date'],
            'stage' => ['required', new Enum(JobStage::class)],
            'rejection_reason' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'position' => 'المسمى الوظيفي',
            'company' => 'الشركة',
            'applied_via' => 'قدّمت عن طريق',
            'url' => 'الرابط',
            'stage' => 'المرحلة',
        ];
    }

    public function setJob(JobApplication $job): void
    {
        $this->job = $job;
        $this->goal_id = $job->goal_id;
        $this->position = $job->position;
        $this->company = $job->company;
        $this->applied_via = $job->applied_via;
        $this->url = $job->url;
        $this->description = $job->description;
        $this->applied_on = $job->applied_on?->toDateString();
        $this->stage = $job->stage->value;
        $this->rejection_reason = $job->rejection_reason;
    }

    public function persist(int $userId): JobApplication
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->job) {
            $this->job->update($data);

            return $this->job;
        }

        return JobApplication::create($data);
    }
}
