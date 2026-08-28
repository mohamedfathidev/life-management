<?php

namespace App\Livewire\Forms;

use App\Models\RecoveryLog;
use Illuminate\Support\Carbon;
use Livewire\Form;

class RecoveryLogForm extends Form
{
    public ?RecoveryLog $log = null;

    public int $recovery_id = 0;
    public string $date = '';
    public ?int $urge_level = null;
    public ?int $mood = null;
    public ?string $trigger_note = null;
    public ?string $note = null;
    public bool $is_setback = false;
    public ?string $hardest_from = null;
    public ?string $hardest_to = null;
    public ?bool $stayed_up_late = null;
    public ?bool $had_dinner = null;
    public ?bool $prepared_for_sleep = null;
    public ?string $sleep_location = null;
    public ?string $sleep_spot = null;
    /** @var array<int, string> */
    public array $avoidance_reasons = [];
    /** @var array<int, string> */
    public array $protection_actions = [];

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'recovery_id' => ['required', 'integer', 'exists:recoveries,id'],
            'date' => ['required', 'date'],
            'urge_level' => ['nullable', 'integer', 'between:1,10'],
            'mood' => ['nullable', 'integer', 'between:1,10'],
            'trigger_note' => ['nullable', 'string', 'max:5000'],
            'note' => ['nullable', 'string', 'max:5000'],
            'is_setback' => ['boolean'],
            'hardest_from' => ['nullable', 'date_format:H:i'],
            'hardest_to' => ['nullable', 'date_format:H:i'],
            'stayed_up_late' => ['nullable', 'boolean'],
            'had_dinner' => ['nullable', 'boolean'],
            'prepared_for_sleep' => ['nullable', 'boolean'],
            'sleep_location' => ['nullable', 'in:home,outside'],
            'sleep_spot' => ['nullable', 'in:bed,elsewhere'],
            'avoidance_reasons' => ['nullable', 'array'],
            'avoidance_reasons.*' => ['nullable', 'string', 'max:2000'],
            'protection_actions' => ['nullable', 'array'],
            'protection_actions.*' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'date' => 'التاريخ',
            'urge_level' => 'شدّة الرغبة',
            'mood' => 'المزاج',
            'trigger_note' => 'المُحفّز',
            'note' => 'ملاحظة',
            'sleep_location' => 'مكان النوم',
            'sleep_spot' => 'مكان النوم بالتحديد',
            'avoidance_reasons' => 'طرق تجنب السقوط',
            'protection_actions' => 'خطوات الحماية من الأفكار',
        ];
    }

    public function setLog(RecoveryLog $log): void
    {
        $this->log = $log;
        $this->recovery_id = $log->recovery_id;
        $this->date = $log->date->toDateString();
        $this->urge_level = $log->urge_level;
        $this->mood = $log->mood;
        $this->trigger_note = $log->trigger_note;
        $this->note = $log->note;
        $this->is_setback = $log->is_setback;
        $this->hardest_from = $log->hardest_from ? substr($log->hardest_from, 0, 5) : null;
        $this->hardest_to = $log->hardest_to ? substr($log->hardest_to, 0, 5) : null;
        $this->stayed_up_late = $log->stayed_up_late;
        $this->had_dinner = $log->had_dinner;
        $this->prepared_for_sleep = $log->prepared_for_sleep;
        $this->sleep_location = $log->sleep_location;
        $this->sleep_spot = $log->sleep_spot;
        $this->avoidance_reasons = $log->avoidance_reasons ?: [];
        $this->protection_actions = $log->protection_actions ?: [];
    }

    public function persist(): RecoveryLog
    {
        $data = $this->validate();

        $data['avoidance_reasons'] = $this->cleanBullets($data['avoidance_reasons'] ?? []);
        $data['protection_actions'] = $this->cleanBullets($data['protection_actions'] ?? []);

        if ($this->log) {
            $this->log->update($data);

            return $this->log;
        }

        return RecoveryLog::create($data);
    }

    public function prepareForCreate(int $recoveryId): void
    {
        $this->reset();
        $this->recovery_id = $recoveryId;
        $this->date = Carbon::today()->toDateString();
    }

    /**
     * @param  array<int, string|null>  $bullets
     * @return array<int, string>|null
     */
    private function cleanBullets(array $bullets): ?array
    {
        $bullets = array_values(array_filter(
            array_map(fn ($bullet) => trim((string) $bullet), $bullets),
            fn ($bullet) => $bullet !== ''
        ));

        return $bullets === [] ? null : $bullets;
    }
}
