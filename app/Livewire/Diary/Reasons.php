<?php

namespace App\Livewire\Diary;

use App\Models\DiaryReason;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * "ليه مبتغيرش… ولا حياتي بتتغير؟" — a self-reflection tree: a fixed root
 * question with reasons (and sub-reasons) branching off it.
 */
#[Layout('layouts.app')]
class Reasons extends Component
{
    public ?int $editingId = null;

    public string $body = '';

    public ?int $parentId = null;

    public function save(): void
    {
        $data = $this->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parentId' => ['nullable', 'integer', 'exists:diary_reasons,id'],
        ], attributes: ['body' => 'السبب', 'parentId' => 'فرع تحت سبب']);

        if ($this->editingId) {
            $reason = DiaryReason::ownedBy(Auth::user())->findOrFail($this->editingId);
            $reason->update(['body' => $data['body']]);
        } else {
            $maxOrder = DiaryReason::ownedBy(Auth::user())->where('parent_id', $data['parentId'])->max('sort_order');
            DiaryReason::create([
                'user_id' => Auth::id(),
                'parent_id' => $data['parentId'],
                'body' => $data['body'],
                'sort_order' => ((int) $maxOrder) + 1,
            ]);
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $reason = DiaryReason::ownedBy(Auth::user())->findOrFail($id);
        $this->editingId = $reason->id;
        $this->body = $reason->body;
    }

    public function delete(int $id): void
    {
        DiaryReason::ownedBy(Auth::user())->where('id', $id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'body', 'parentId');
        $this->resetValidation();
    }

    public function render(): View
    {
        $reasons = DiaryReason::ownedBy(Auth::user())->orderBy('sort_order')->get();

        return view('livewire.diary.reasons', [
            'tree' => $this->buildTree($reasons),
            'flatOptions' => $this->flattenForSelect($reasons),
        ]);
    }

    /** @param Collection<int, DiaryReason> $reasons */
    private function buildTree(Collection $reasons, ?int $parentId = null): Collection
    {
        return $reasons->where('parent_id', $parentId)->map(fn (DiaryReason $r) => (object) [
            'reason' => $r,
            'children' => $this->buildTree($reasons, $r->id),
        ])->values();
    }

    /** @param Collection<int, DiaryReason> $reasons */
    private function flattenForSelect(Collection $reasons, ?int $parentId = null, int $depth = 0): Collection
    {
        return $reasons->where('parent_id', $parentId)->reduce(function (Collection $carry, DiaryReason $r) use ($reasons, $depth) {
            $carry->push((object) ['id' => $r->id, 'label' => str_repeat('— ', $depth).Str::limit($r->body, 50)]);

            return $carry->merge($this->flattenForSelect($reasons, $r->id, $depth + 1));
        }, collect());
    }
}
