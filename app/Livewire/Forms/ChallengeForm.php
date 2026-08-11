<?php

namespace App\Livewire\Forms;

use App\Enums\ChallengeStatus;
use App\Models\Challenge;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class ChallengeForm extends Form
{
    public ?Challenge $challenge = null;

    public string $title = '';
    public ?string $description = null;
    public string $start_date = '';
    public ?int $duration_days = 30;
    public string $status = 'active';
    public string $color = '#3F7D7A';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'between:1,365'],
            'status' => ['required', new Enum(ChallengeStatus::class)],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['title' => 'العنوان', 'start_date' => 'تاريخ البداية', 'duration_days' => 'المدة'];
    }

    public function setChallenge(Challenge $challenge): void
    {
        $this->challenge = $challenge;
        $this->title = $challenge->title;
        $this->description = $challenge->description;
        $this->start_date = $challenge->start_date->toDateString();
        $this->duration_days = $challenge->duration_days;
        $this->status = $challenge->status->value;
        $this->color = $challenge->color;
    }

    public function prepareForCreate(): void
    {
        $this->reset();
        $this->start_date = Carbon::today()->toDateString();
        $this->duration_days = 30;
        $this->status = 'active';
        $this->color = '#3F7D7A';
    }

    public function persist(int $userId): Challenge
    {
        $data = $this->validate();
        $data['user_id'] = $userId;

        if ($this->challenge) {
            $this->challenge->update($data);

            return $this->challenge;
        }

        return Challenge::create($data);
    }
}
