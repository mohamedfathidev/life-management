<?php

namespace App\Livewire\Forms;

use App\Models\QuranLog;
use Illuminate\Support\Carbon;
use Livewire\Form;

class QuranLogForm extends Form
{
    public ?QuranLog $log = null;

    public string $date = '';
    public ?string $from_surah = null;
    public ?int $from_ayah = null;
    public ?string $to_surah = null;
    public ?int $to_ayah = null;
    public ?int $pages = null;
    public ?string $note = null;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'from_surah' => ['nullable', 'string', 'max:255'],
            'from_ayah' => ['nullable', 'integer', 'min:1', 'max:300'],
            'to_surah' => ['nullable', 'string', 'max:255'],
            'to_ayah' => ['nullable', 'integer', 'min:1', 'max:300'],
            'pages' => ['nullable', 'integer', 'min:0', 'max:604'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['date' => 'التاريخ', 'pages' => 'الصفحات'];
    }

    public function setLog(QuranLog $log): void
    {
        $this->log = $log;
        $this->date = $log->date->toDateString();
        $this->from_surah = $log->from_surah;
        $this->from_ayah = $log->from_ayah;
        $this->to_surah = $log->to_surah;
        $this->to_ayah = $log->to_ayah;
        $this->pages = $log->pages;
        $this->note = $log->note;
    }

    public function prepareForCreate(): void
    {
        $this->reset();
        $this->date = Carbon::today()->toDateString();
    }

    public function persist(int $userId): QuranLog
    {
        $data = $this->validate();
        $data['user_id'] = $userId;
        $data['pages'] = $data['pages'] ?? 0;

        if ($this->log) {
            $this->log->update($data);

            return $this->log;
        }

        return QuranLog::create($data);
    }
}
