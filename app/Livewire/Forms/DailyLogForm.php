<?php

namespace App\Livewire\Forms;

use App\Enums\ModuleType;
use App\Models\DailyLog;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class DailyLogForm extends Form
{
    public ?DailyLog $log = null;

    public ?int $goal_id = null;
    public string $module_type = 'general';
    public string $date = '';
    public ?int $mood = null;
    public ?int $difficulty = null;
    public ?string $note = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'goal_id' => ['nullable', 'integer', 'exists:goals,id'],
            'module_type' => ['required', new Enum(ModuleType::class)],
            'date' => ['required', 'date'],
            'mood' => ['nullable', 'integer', 'between:1,10'],
            'difficulty' => ['nullable', 'integer', 'between:1,10'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'goal_id' => 'الهدف',
            'module_type' => 'الوحدة',
            'date' => 'التاريخ',
            'mood' => 'المزاج',
            'difficulty' => 'الصعوبة',
            'note' => 'ملاحظة',
        ];
    }

    public function setLog(DailyLog $log): void
    {
        $this->log = $log;
        $this->goal_id = $log->goal_id;
        $this->module_type = $log->module_type->value;
        $this->date = $log->date->toDateString();
        $this->mood = $log->mood;
        $this->difficulty = $log->difficulty;
        $this->note = $log->note;
    }

    public function persist(int $userId): DailyLog
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->log) {
            $this->log->update($data);

            return $this->log;
        }

        return DailyLog::create($data);
    }
}
