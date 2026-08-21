<?php

namespace App\Livewire\Forms;

use App\Models\RecoveryStory;
use Illuminate\Support\Carbon;
use Livewire\Form;

class RecoveryStoryForm extends Form
{
    public ?RecoveryStory $story = null;

    public string $date = '';

    public ?string $title = null;

    public ?string $content = null;

    public ?int $mood = null;

    public ?int $recovery_id = null;

    public string $tagsInput = '';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:100000'],
            'mood' => ['nullable', 'integer', 'between:1,10'],
            'recovery_id' => ['nullable', 'integer', 'exists:recoveries,id'],
            'tagsInput' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['date' => 'التاريخ', 'title' => 'العنوان', 'mood' => 'المزاج', 'recovery_id' => 'فترة التعافي'];
    }

    public function setStory(RecoveryStory $story): void
    {
        $this->story = $story;
        $this->date = $story->date->toDateString();
        $this->title = $story->title;
        $this->content = $story->content;
        $this->mood = $story->mood;
        $this->recovery_id = $story->recovery_id;
        $tags = array_map(fn ($t) => '#'.ltrim(trim($t), '#'), $story->tags ?? []);
        $this->tagsInput = implode(' ', $tags);
    }

    public function prepareForCreate(?int $recoveryId = null): void
    {
        $this->reset();
        $this->date = Carbon::today()->toDateString();
        $this->recovery_id = $recoveryId;
    }

    public function persist(int $userId): RecoveryStory
    {
        $data = $this->validate();

        $payload = [
            'user_id' => $userId,
            'date' => $data['date'],
            'title' => $data['title'],
            'content' => $data['content'],
            'mood' => $data['mood'],
            'recovery_id' => $data['recovery_id'],
            'tags' => $this->parseTags($this->tagsInput),
        ];

        if ($this->story) {
            $this->story->update($payload);

            return $this->story;
        }

        return RecoveryStory::create($payload);
    }

    /** @return array<int, string> */
    private function parseTags(string $input): array
    {
        $parts = preg_split('/[،,\s]+/u', $input) ?: [];

        return collect($parts)
            ->map(fn ($t) => ltrim(trim($t), '#'))
            ->filter(fn ($t) => $t !== '')
            ->unique()
            ->values()
            ->all();
    }
}
