<?php

namespace App\Livewire\Forms;

use App\Models\StudyTrack;
use Livewire\Form;

class StudyTrackForm extends Form
{
    public ?StudyTrack $track = null;

    public string $title = '';
    public ?string $field = null;
    public ?string $plan = null;
    public ?string $resources = null;
    public ?string $target = null;
    public ?string $certificate = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public bool $is_completed = false;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'field' => ['nullable', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'max:20000'],
            'resources' => ['nullable', 'string', 'max:20000'],
            'target' => ['nullable', 'string', 'max:5000'],
            'certificate' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_completed' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'العنوان',
            'field' => 'المجال',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
        ];
    }

    public function setTrack(StudyTrack $track): void
    {
        $this->track = $track;
        $this->title = $track->title;
        $this->field = $track->field;
        $this->plan = $track->plan;
        $this->resources = $track->resources;
        $this->target = $track->target;
        $this->certificate = $track->certificate;
        $this->start_date = $track->start_date?->toDateString();
        $this->end_date = $track->end_date?->toDateString();
        $this->is_completed = $track->is_completed;
    }

    public function persist(int $userId): StudyTrack
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->track) {
            $this->track->update($data);

            return $this->track;
        }

        return StudyTrack::create($data);
    }
}
