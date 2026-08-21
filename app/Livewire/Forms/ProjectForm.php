<?php

namespace App\Livewire\Forms;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ProjectForm extends Form
{
    public ?Project $project = null;

    public ?int $goal_id = null;
    public string $title = '';
    public ?string $pitch = null;
    public ?string $why = null;
    public ?string $url = null;
    public string $status = 'idea';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'goal_id' => ['nullable', 'integer', 'exists:goals,id'],
            'title' => ['required', 'string', 'max:255'],
            'pitch' => ['nullable', 'string', 'max:2000'],
            'why' => ['nullable', 'string', 'max:2000'],
            'url' => ['nullable', 'url', 'max:2000'],
            'status' => ['required', new Enum(ProjectStatus::class)],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'اسم المشروع',
            'pitch' => 'الفكرة',
            'why' => 'ليه عايز تعملها',
            'url' => 'الرابط',
            'status' => 'الحالة',
        ];
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
        $this->goal_id = $project->goal_id;
        $this->title = $project->title;
        $this->pitch = $project->pitch;
        $this->why = $project->why;
        $this->url = $project->url;
        $this->status = $project->status->value;
    }

    public function prepareForCreate(): void
    {
        $this->reset();
        $this->status = 'idea';
    }

    public function persist(int $userId): Project
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->project) {
            $this->project->update($data);

            return $this->project;
        }

        return Project::create($data);
    }
}
