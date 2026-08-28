<?php

namespace App\Livewire\Religion;

use App\Livewire\Forms\DuaaForm;
use App\Models\Duaa;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Duaas extends Component
{
    public DuaaForm $form;

    public bool $open = false;

    #[Url]
    public string $tag = '';

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->form->reset();
        $this->open = true;
    }

    public function editDuaa(int $id): void
    {
        $duaa = Duaa::ownedBy(Auth::user())->findOrFail($id);
        $this->resetValidation();
        $this->form->setDuaa($duaa);
        $this->open = true;
    }

    public function deleteDuaa(int $id): void
    {
        Duaa::ownedBy(Auth::user())->where('id', $id)->delete();
    }

    public function toggleFavorite(int $id): void
    {
        $duaa = Duaa::ownedBy(Auth::user())->findOrFail($id);
        $duaa->update(['is_favorite' => ! $duaa->is_favorite]);
    }

    /**
     * Imports the curated, sourced duas from public/files/duaas.json
     * (Quran/hadith text with citations) — skips any already imported,
     * matched by title, so this is safe to click more than once.
     */
    public function importTrusted(): void
    {
        $path = public_path('files/duaas.json');
        if (! is_file($path)) {
            return;
        }

        $data = json_decode((string) file_get_contents($path), true);
        $categories = $data['categories'] ?? [];

        $existingTitles = Duaa::ownedBy(Auth::user())->pluck('title')->all();

        foreach ($categories as $category => $items) {
            foreach ($items as $item) {
                if (in_array($item['title'], $existingTitles, true)) {
                    continue;
                }

                Duaa::create([
                    'user_id' => Auth::id(),
                    'title' => $item['title'],
                    'content' => trim($item['content'])."\n\n📖 ".$item['source'],
                    'tags' => [$category],
                ]);
            }
        }
    }

    public function save(): void
    {
        $this->form->persist(Auth::id());
        $this->open = false;
    }

    public function close(): void
    {
        $this->open = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render(): View
    {
        // Filtered in PHP, not via whereJsonContains(): MySQL stores this JSON
        // column's Arabic text \uXXXX-escaped, and JSON_CONTAINS doesn't match
        // across that encoding difference — it silently returns nothing.
        $duaas = Duaa::query()
            ->ownedBy(Auth::user())
            ->orderByDesc('is_favorite')
            ->latest()
            ->get()
            ->filter(fn (Duaa $d) => $this->tag === '' || in_array($this->tag, $d->tags ?? [], true))
            ->values();

        $allTags = Duaa::query()->ownedBy(Auth::user())->pluck('tags')->flatten()->filter()->unique()->sort()->values();

        return view('livewire.religion.duaas', [
            'duaas' => $duaas,
            'allTags' => $allTags,
        ]);
    }
}
