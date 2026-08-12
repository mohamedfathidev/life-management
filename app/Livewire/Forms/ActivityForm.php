<?php

namespace App\Livewire\Forms;

use App\Enums\ActivityStage;
use App\Enums\ActivityType;
use App\Models\Activity;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ActivityForm extends Form
{
    public ?Activity $activity = null;

    public ?int $goal_id = null;
    public string $title = '';
    public string $type = 'competition';
    public ?string $organizer = null;
    public ?string $location = null;
    public ?string $url = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public string $stage = 'interested';
    public ?string $result = null;
    public ?string $notes = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'goal_id' => ['nullable', 'integer', 'exists:goals,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(ActivityType::class)],
            'organizer' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:2000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'stage' => ['required', new Enum(ActivityStage::class)],
            'result' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:20000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'اسم النشاط',
            'type' => 'النوع',
            'organizer' => 'الجهة المنظمة',
            'location' => 'المكان',
            'url' => 'الرابط',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'stage' => 'المرحلة',
            'result' => 'النتيجة',
        ];
    }

    public function setActivity(Activity $activity): void
    {
        $this->activity = $activity;
        $this->goal_id = $activity->goal_id;
        $this->title = $activity->title;
        $this->type = $activity->type->value;
        $this->organizer = $activity->organizer;
        $this->location = $activity->location;
        $this->url = $activity->url;
        $this->start_date = $activity->start_date?->toDateString();
        $this->end_date = $activity->end_date?->toDateString();
        $this->stage = $activity->stage->value;
        $this->result = $activity->result;
        $this->notes = $activity->notes;
    }

    public function persist(int $userId): Activity
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->activity) {
            $this->activity->update($data);

            return $this->activity;
        }

        return Activity::create($data);
    }
}
