<?php

namespace App\Livewire\Forms;

use App\Enums\TaskKind;
use App\Enums\TaskStatus;
use App\Models\Day;
use App\Models\Goal;
use App\Models\Task;
use Closure;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class TaskForm extends Form
{
    public ?Task $task = null;

    public ?int $day_id = null;
    public ?int $goal_id = null;
    public ?int $study_track_id = null;
    public string $title = '';
    public ?string $start_time = null;
    public ?string $end_time = null;
    public string $kind = 'other';
    public bool $is_important = false;
    public int $progress = 0;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'day_id' => ['nullable', 'integer', 'exists:days,id'],
            'goal_id' => ['nullable', 'integer', 'exists:goals,id', $this->withinGoalRange()],
            'study_track_id' => ['nullable', 'integer', 'exists:study_tracks,id'],
            'title' => ['required', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', $this->endAfterStart()],
            'kind' => ['required', new Enum(TaskKind::class)],
            'is_important' => ['boolean'],
            'progress' => ['required', 'integer', 'between:0,100'],
        ];
    }

    /** End time (when set) must be later than the start time. */
    protected function endAfterStart(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ($value && $this->start_time && $value <= $this->start_time) {
                $fail('وقت النهاية لازم يكون بعد وقت البداية.');
            }
        };
    }

    /**
     * Ensures a task linked to a goal falls inside that goal's date window:
     * you can't schedule a task on a day outside the (sub-)goal's start→end range.
     */
    protected function withinGoalRange(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! $value || ! $this->day_id) {
                return; // no goal, or a pool task with no date → nothing to constrain
            }

            $goal = Goal::find($value);
            $day = Day::find($this->day_id);

            if (! $goal || ! $day) {
                return;
            }

            if (! $goal->acceptsDate($day->date)) {
                $fail('لا يمكن ربط التاسك بهذا الهدف: '.$goal->rangeMessage($day->date));
            }
        };
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'عنوان التاسك',
            'goal_id' => 'الهدف',
            'start_time' => 'وقت البداية',
            'end_time' => 'وقت النهاية',
            'kind' => 'النوع',
            'progress' => 'نسبة الإنجاز',
        ];
    }

    public function setTask(Task $task): void
    {
        $this->task = $task;
        $this->day_id = $task->day_id;
        $this->goal_id = $task->goal_id;
        $this->study_track_id = $task->study_track_id;
        $this->title = $task->title;
        $this->start_time = $task->start_time ? \Illuminate\Support\Carbon::parse($task->start_time)->format('H:i') : null;
        $this->end_time = $task->end_time ? \Illuminate\Support\Carbon::parse($task->end_time)->format('H:i') : null;
        $this->kind = $task->kind->value;
        $this->is_important = $task->is_important;
        $this->progress = $task->progress;
    }

    public function persist(int $userId): Task
    {
        $data = $this->validate();
        $data['user_id'] = $userId;
        $data['status'] = TaskStatus::fromProgress($this->progress)->value;

        // A task linked to a goal / study track takes the matching kind.
        if ($this->goal_id) {
            $data['kind'] = TaskKind::Goal->value;
        } elseif ($this->study_track_id) {
            $data['kind'] = TaskKind::Study->value;
        }

        if ($this->task) {
            $this->task->update($data);

            return $this->task;
        }

        return Task::create($data);
    }
}
