<?php

namespace App\Livewire\Forms;

use App\Models\Duaa;
use Illuminate\Support\Str;
use Livewire\Form;

class DuaaForm extends Form
{
    public ?Duaa $duaa = null;

    public string $title = '';
    public ?string $content = null;
    public string $tagsInput = '';

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:20000'],
            'tagsInput' => ['nullable', 'string', 'max:500'],
        ];
    }

    /** @return array<string, string> */
    public function validationAttributes(): array
    {
        return ['title' => 'العنوان', 'content' => 'النص'];
    }

    public function setDuaa(Duaa $duaa): void
    {
        $this->duaa = $duaa;
        $this->title = $duaa->title;
        $this->content = $duaa->content;
        $this->tagsInput = implode('، ', $duaa->tags ?? []);
    }

    public function persist(int $userId): Duaa
    {
        $data = $this->validate();

        $payload = [
            'user_id' => $userId,
            'title' => $data['title'],
            'content' => $data['content'],
            'tags' => $this->parseTags($this->tagsInput),
        ];

        if ($this->duaa) {
            $this->duaa->update($payload);

            return $this->duaa;
        }

        return Duaa::create($payload);
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
