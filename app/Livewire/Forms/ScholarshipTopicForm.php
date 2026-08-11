<?php

namespace App\Livewire\Forms;

use App\Models\ScholarshipTopic;
use Illuminate\Support\Str;
use Livewire\Form;

class ScholarshipTopicForm extends Form
{
    public ?ScholarshipTopic $topic = null;

    public string $title = '';
    public ?string $content = null;
    public string $tagsInput = '';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:50000'],
            'tagsInput' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'العنوان',
            'content' => 'المحتوى',
            'tagsInput' => 'الوسوم',
        ];
    }

    public function setTopic(ScholarshipTopic $topic): void
    {
        $this->topic = $topic;
        $this->title = $topic->title;
        $this->content = $topic->content;
        $this->tagsInput = implode('، ', $topic->tags ?? []);
    }

    public function persist(int $userId): ScholarshipTopic
    {
        $data = $this->validate();

        $payload = [
            'user_id' => $userId,
            'title' => $data['title'],
            'content' => $data['content'],
            'tags' => $this->parseTags($this->tagsInput),
        ];

        if ($this->topic) {
            $this->topic->update($payload);

            return $this->topic;
        }

        return ScholarshipTopic::create($payload);
    }

    /** @return array<int, string> */
    private function parseTags(string $input): array
    {
        $parts = preg_split('/[،,]+/u', $input) ?: [];

        return collect($parts)
            ->map(fn ($t) => Str::of($t)->trim()->value())
            ->filter(fn ($t) => $t !== '')
            ->unique()
            ->values()
            ->all();
    }
}
