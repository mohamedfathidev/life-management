<?php

namespace App\Livewire\Career;

use App\Models\ItemDocument;
use App\Models\JobApplication;
use App\Models\Scholarship;
use App\Models\ScholarshipDocument;
use App\Models\ScholarshipTopic;
use App\Models\VolunteerActivity;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * An embeddable required-documents checklist for a career item
 * (scholarship / job / volunteer activity). Each row can be linked to a document
 * in the general library ("الأوراق") — then its readiness syncs both ways.
 */
class ItemDocuments extends Component
{
    public string $documentableType;
    public int $documentableId;
    public string $newName = '';
    public ?int $newLinkId = null; // pick from the general library

    // Presentation (allows reuse as a generic checklist, e.g. a learning plan).
    public string $heading = '📄 الأوراق المطلوبة';
    public string $placeholder = 'أضف ورقة مطلوبة (مثال: كشف الدرجات)…';
    public bool $showLibrary = true;

    /** @var array<string, class-string<Model>> */
    private const MODELS = [
        'scholarship' => Scholarship::class,
        'job' => JobApplication::class,
        'volunteer' => VolunteerActivity::class,
        'topic' => ScholarshipTopic::class,
    ];

    private function parent(): ?Model
    {
        $model = self::MODELS[$this->documentableType] ?? null;

        return $model ? $model::query()->where('user_id', Auth::id())->find($this->documentableId) : null;
    }

    public function add(): void
    {
        $parent = $this->parent();
        if (! $parent) {
            return;
        }

        $position = (int) $parent->documents()->max('position') + 1;

        if ($this->newLinkId) {
            // Link a document from the general library.
            $lib = ScholarshipDocument::ownedBy(Auth::user())->find($this->newLinkId);
            if ($lib) {
                $parent->documents()->create([
                    'user_id' => Auth::id(),
                    'scholarship_document_id' => $lib->id,
                    'name' => $lib->name,
                    'position' => $position,
                ]);
            }
        } else {
            $this->validate(['newName' => ['required', 'string', 'max:255']], attributes: ['newName' => 'اسم الورقة']);
            $parent->documents()->create([
                'user_id' => Auth::id(),
                'name' => $this->newName,
                'position' => $position,
            ]);
        }

        $this->reset('newName', 'newLinkId');
    }

    /** Toggle readiness — syncs the library document when linked. */
    public function toggle(int $id): void
    {
        $doc = ItemDocument::where('user_id', Auth::id())->find($id);
        if (! $doc) {
            return;
        }

        if ($doc->scholarship_document_id) {
            $lib = ScholarshipDocument::ownedBy(Auth::user())->find($doc->scholarship_document_id);
            $lib?->update(['is_ready' => ! $lib->is_ready]);
        } else {
            $doc->update(['is_done' => ! $doc->is_done]);
        }
    }

    public function saveNote(int $id, ?string $note): void
    {
        ItemDocument::where('user_id', Auth::id())->where('id', $id)
            ->update(['note' => $note !== '' ? $note : null]);
    }

    public function delete(int $id): void
    {
        ItemDocument::where('user_id', Auth::id())->where('id', $id)->delete();
    }

    public function render(): View
    {
        $parent = $this->parent();
        $documents = $parent ? $parent->documents()->with('generalDocument')->get() : collect();

        return view('livewire.career.item-documents', [
            'documents' => $documents,
            'doneCount' => $documents->filter(fn (ItemDocument $d) => $d->isReady())->count(),
            'library' => $this->showLibrary ? ScholarshipDocument::ownedBy(Auth::user())->orderBy('name')->get() : collect(),
        ]);
    }
}
