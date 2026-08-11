<?php

namespace App\Livewire\Forms;

use App\Enums\GoalCategory;
use App\Enums\GoalStatus;
use App\Models\Goal;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Form;

class GoalForm extends Form
{
    public ?Goal $goal = null;

    /** Set when creating a sub-goal under an existing goal. */
    public ?int $parent_id = null;

    #[Validate]
    public string $title = '';

    #[Validate]
    public string $category = 'general';

    #[Validate]
    public ?string $description = null;

    #[Validate]
    public string $color = '#3F7D7A';

    #[Validate]
    public string $status = 'active';

    #[Validate]
    public ?string $start_date = null;

    #[Validate]
    public ?string $target_date = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:goals,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', new Enum(GoalCategory::class)],
            'description' => ['nullable', 'string', 'max:5000'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'status' => ['required', new Enum(GoalStatus::class)],
            'start_date' => ['nullable', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'العنوان',
            'category' => 'الفئة',
            'description' => 'الوصف',
            'color' => 'اللون',
            'status' => 'الحالة',
            'start_date' => 'تاريخ البداية',
            'target_date' => 'تاريخ النهاية',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'target_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
        ];
    }

    public function setGoal(Goal $goal): void
    {
        $this->goal = $goal;
        $this->parent_id = $goal->parent_id;
        $this->title = $goal->title;
        $this->category = $goal->category->value;
        $this->description = $goal->description;
        $this->color = $goal->color;
        $this->status = $goal->status->value;
        $this->start_date = $goal->start_date?->toDateString();
        $this->target_date = $goal->target_date?->toDateString();
    }

    /** Persist the form to a new or existing goal for the given owner. */
    public function persist(int $userId): Goal
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->goal) {
            $this->goal->update($data);

            return $this->goal;
        }

        return Goal::create($data);
    }
}
