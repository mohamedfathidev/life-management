<?php

namespace App\Livewire\Forms;

use App\Models\RecoveryDream;
use Livewire\Form;

class RecoveryDreamForm extends Form
{
    public ?RecoveryDream $dream = null;

    public string $icon = '🌅';

    public ?string $title = null;

    public string $benefitsInput = '';

    public ?int $recovery_id = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'icon' => ['nullable', 'string', 'max:10'],
            'title' => ['required', 'string', 'max:255'],
            'benefitsInput' => ['nullable', 'string', 'max:2000'],
            'recovery_id' => ['nullable', 'integer', 'exists:recoveries,id'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['title' => 'الحلم', 'recovery_id' => 'فترة التعافي'];
    }

    public function setDream(RecoveryDream $dream): void
    {
        $this->dream = $dream;
        $this->icon = $dream->icon ?? '🌅';
        $this->title = $dream->title;
        $this->benefitsInput = implode("\n", $dream->benefits ?? []);
        $this->recovery_id = $dream->recovery_id;
    }

    public function prepareForCreate(?int $recoveryId = null): void
    {
        $this->reset();
        $this->icon = '🌅';
        $this->recovery_id = $recoveryId;
    }

    public function persist(int $userId): RecoveryDream
    {
        $data = $this->validate();

        $payload = [
            'user_id' => $userId,
            'icon' => $data['icon'] ?: '🌅',
            'title' => $data['title'],
            'benefits' => $this->parseBenefits($this->benefitsInput),
            'recovery_id' => $data['recovery_id'],
        ];

        if ($this->dream) {
            $this->dream->update($payload);

            return $this->dream;
        }

        return RecoveryDream::create($payload);
    }

    /** @return array<int, string> */
    private function parseBenefits(string $input): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $input) ?: [])
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->values()
            ->all();
    }
}
