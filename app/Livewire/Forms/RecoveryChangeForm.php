<?php

namespace App\Livewire\Forms;

use App\Enums\RecoveryStatus;
use App\Models\RecoveryChange;
use Illuminate\Support\Carbon;
use Livewire\Form;

class RecoveryChangeForm extends Form
{
    public ?RecoveryChange $change = null;

    public string $icon = '🔥';

    public ?string $title = null;

    public ?string $why = null;

    public string $status = 'active';

    public string $started_at = '';

    public ?string $target_date = null;

    public ?int $recovery_id = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'icon' => ['nullable', 'string', 'max:10'],
            'title' => ['required', 'string', 'max:255'],
            'why' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(RecoveryStatus::cases(), 'value'))],
            'started_at' => ['required', 'date'],
            'target_date' => ['nullable', 'date', 'after_or_equal:started_at'],
            'recovery_id' => ['nullable', 'integer', 'exists:recoveries,id'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'التغيير',
            'started_at' => 'تاريخ البداية',
            'target_date' => 'التاريخ المستهدف',
            'recovery_id' => 'فترة التعافي',
        ];
    }

    public function setChange(RecoveryChange $change): void
    {
        $this->change = $change;
        $this->icon = $change->icon ?? '🔥';
        $this->title = $change->title;
        $this->why = $change->why;
        $this->status = $change->status->value;
        $this->started_at = $change->started_at->toDateString();
        $this->target_date = $change->target_date?->toDateString();
        $this->recovery_id = $change->recovery_id;
    }

    public function prepareForCreate(?int $recoveryId = null): void
    {
        $this->reset();
        $this->icon = '🔥';
        $this->status = 'active';
        $this->started_at = Carbon::today()->toDateString();
        $this->recovery_id = $recoveryId;
    }

    public function persist(int $userId): RecoveryChange
    {
        $data = $this->validate();

        $payload = [
            'user_id' => $userId,
            'icon' => $data['icon'] ?: '🔥',
            'title' => $data['title'],
            'why' => $data['why'],
            'status' => $data['status'],
            'started_at' => $data['started_at'],
            'target_date' => $data['target_date'],
            'recovery_id' => $data['recovery_id'],
        ];

        if ($this->change) {
            $this->change->update($payload);

            return $this->change;
        }

        return RecoveryChange::create($payload);
    }
}
