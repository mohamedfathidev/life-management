<?php

namespace App\Livewire\Forms;

use App\Models\DiaryEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Form;

class DiaryForm extends Form
{
    public ?DiaryEntry $entry = null;

    public string $date = '';
    public ?string $title = null;
    public ?string $content = null;
    public ?int $mood = null;
    public string $tagsInput = '';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:100000'],
            'mood' => ['nullable', 'integer', 'between:1,10'],
            'tagsInput' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['date' => 'التاريخ', 'title' => 'العنوان', 'mood' => 'المزاج'];
    }

    public function setEntry(DiaryEntry $entry): void
    {
        $this->entry = $entry;
        $this->date = $entry->date->toDateString();
        $this->title = $entry->title;
        $this->content = $entry->content;
        $this->mood = $entry->mood;
        $tags = array_map(fn ($t) => '#'.ltrim(trim($t), '#'), $entry->tags ?? []);
        $this->tagsInput = implode(' ', $tags);
    }

    public function prepareForCreate(): void
    {
        $this->reset();
        $this->date = Carbon::today()->toDateString();
    }

    public function persist(int $userId): DiaryEntry
    {
        $data = $this->validate();

        $payload = [
            'user_id' => $userId,
            'date' => $data['date'],
            'title' => $data['title'],
            'content' => $data['content'],
            'mood' => $data['mood'],
            'tags' => $this->parseTags($this->tagsInput),
        ];

        if ($this->entry) {
            $this->entry->update($payload);

            return $this->entry;
        }

        return DiaryEntry::create($payload);
    }

    /** @return array<int, string> */
    private function parseTags(string $input): array
    {
        // Split by commas, or whitespace when hashtags are entered
        $parts = preg_split('/[،,\s]+/u', $input) ?: [];

        return collect($parts)
            ->map(fn ($t) => ltrim(trim($t), '#'))
            ->filter(fn ($t) => $t !== '')
            ->unique()
            ->values()
            ->all();
    }
}
