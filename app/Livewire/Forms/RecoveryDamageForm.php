<?php

namespace App\Livewire\Forms;

use App\Models\RecoveryDamage;
use Livewire\Form;

class RecoveryDamageForm extends Form
{
    public ?RecoveryDamage $damage = null;

    public string $title = '';

    public ?string $icon = null;

    public ?int $parent_id = null;

    public int $degree = 50;

    public ?string $description = null;

    public string $lifeWithoutInput = '';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:8'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:recovery_damages,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value === null) {
                        return;
                    }

                    if ($this->damage && (int) $value === $this->damage->id) {
                        $fail('الضرر لا يمكن أن يكون تابعاً لنفسه.');

                        return;
                    }

                    // One level only: the parent must be a main damage.
                    if (RecoveryDamage::find($value)?->parent_id !== null) {
                        $fail('لا يمكن إضافة ضرر فرعي تحت ضرر فرعي.');
                    }
                },
            ],
            'degree' => ['required', 'integer', 'between:0,100'],
            'description' => ['nullable', 'string', 'max:20000'],
            'lifeWithoutInput' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'العنوان',
            'degree' => 'درجة الضرر',
            'description' => 'الوصف',
            'parent_id' => 'الضرر الأب',
        ];
    }

    public function setDamage(RecoveryDamage $damage): void
    {
        $this->damage = $damage;
        $this->title = $damage->title;
        $this->icon = $damage->icon;
        $this->parent_id = $damage->parent_id;
        $this->degree = $damage->degree;
        $this->description = $damage->description;
        $this->lifeWithoutInput = implode("\n", $damage->life_without ?? []);
    }

    public function prepareForCreate(?int $parentId = null): void
    {
        $this->reset();
        $this->degree = 50;
        $this->parent_id = $parentId;
    }

    public function persist(int $userId): RecoveryDamage
    {
        $data = $this->validate();

        $payload = [
            'user_id' => $userId,
            'title' => $data['title'],
            'icon' => $data['icon'],
            'parent_id' => $data['parent_id'],
            'degree' => $data['degree'],
            'description' => $data['description'],
            'life_without' => $this->parseBullets($this->lifeWithoutInput),
        ];

        if ($this->damage) {
            $this->damage->update($payload);

            return $this->damage;
        }

        return RecoveryDamage::create($payload);
    }

    /** @return array<int, string> */
    private function parseBullets(string $input): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $input) ?: [])
            ->map(fn ($line) => ltrim(trim($line), '•-–'))
            ->filter(fn ($line) => $line !== '')
            ->unique()
            ->values()
            ->all();
    }
}
