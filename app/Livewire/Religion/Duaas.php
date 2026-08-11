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
        $duaas = Duaa::query()
            ->ownedBy(Auth::user())
            ->when($this->tag !== '', fn ($q) => $q->withTag($this->tag))
            ->orderByDesc('is_favorite')
            ->latest()
            ->get();

        $allTags = Duaa::query()->ownedBy(Auth::user())->pluck('tags')->flatten()->filter()->unique()->sort()->values();

        return view('livewire.religion.duaas', [
            'duaas' => $duaas,
            'allTags' => $allTags,
        ]);
    }
}
