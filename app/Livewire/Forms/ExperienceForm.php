<?php

namespace App\Livewire\Forms;

use App\Enums\ExperienceKind;
use App\Enums\ExperienceStatus;
use App\Models\ComfortExperience;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ExperienceForm extends Form
{
    public ?ComfortExperience $experience = null;

    public string $title = '';
    public string $kind = 'first_time';
    public string $status = 'wishlist';
    public ?int $difficulty = null;
    public ?string $fear = null;
    public ?string $reflection = null;
    public ?string $target_date = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'kind' => ['required', new Enum(ExperienceKind::class)],
            'status' => ['required', new Enum(ExperienceStatus::class)],
            'difficulty' => ['nullable', 'integer', 'between:1,5'],
            'fear' => ['nullable', 'string', 'max:5000'],
            'reflection' => ['nullable', 'string', 'max:5000'],
            'target_date' => ['nullable', 'date'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['title' => 'التجربة', 'kind' => 'النوع', 'difficulty' => 'الصعوبة'];
    }

    public function setExperience(ComfortExperience $experience): void
    {
        $this->experience = $experience;
        $this->title = $experience->title;
        $this->kind = $experience->kind->value;
        $this->status = $experience->status->value;
        $this->difficulty = $experience->difficulty;
        $this->fear = $experience->fear;
        $this->reflection = $experience->reflection;
        $this->target_date = $experience->target_date?->toDateString();
    }

    public function persist(int $userId): ComfortExperience
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        // Stamp the completion date when marked done.
        if ($data['status'] === ExperienceStatus::Done->value) {
            $data['done_on'] = $this->experience?->done_on?->toDateString() ?? now()->toDateString();
        }

        if ($this->experience) {
            $this->experience->update($data);

            return $this->experience;
        }

        return ComfortExperience::create($data);
    }
}
