<?php

namespace App\Livewire\Forms;

use App\Enums\TopicImportance;
use App\Models\RecoveryTopic;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Livewire\Form;

class RecoveryTopicForm extends Form
{
    public ?RecoveryTopic $topic = null;

    public string $title = '';
    public ?string $content = null;
    public string $tagsInput = '';
    public string $importance = 'medium';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:50000'],
            'tagsInput' => ['nullable', 'string', 'max:500'],
            'importance' => ['required', new Enum(TopicImportance::class)],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return [
            'title' => 'العنوان',
            'content' => 'المحتوى',
            'tagsInput' => 'الوسوم',
            'importance' => 'مستوى الأهمية',
        ];
    }

    public function setTopic(RecoveryTopic $topic): void
    {
        $this->topic = $topic;
        $this->title = $topic->title;
        $this->content = $topic->content;
        $this->tagsInput = implode('، ', $topic->tags ?? []);
        $this->importance = $topic->importance->value;
    }

    public function persist(int $userId): RecoveryTopic
    {
        $data = $this->validate();

        $payload = [
            'user_id' => $userId,
            'title' => $data['title'],
            'content' => $data['content'],
            'tags' => $this->parseTags($this->tagsInput),
            'importance' => $data['importance'],
        ];

        if ($this->topic) {
            $this->topic->update($payload);

            return $this->topic;
        }

        return RecoveryTopic::create($payload);
    }

    /**
     * Split a free tag string (comma / Arabic comma separated) into a clean list.
     *
     * @return array<int, string>
     */
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
