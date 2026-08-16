<?php

namespace App\Livewire\Career;

use App\Models\ItemDocument;
use App\Models\JobApplication;
use App\Models\Scholarship;
use App\Models\ScholarshipDocument;
use App\Models\ScholarshipTopic;
use App\Models\Task;
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
    public bool $allowSubItems = false;

    /** Per-parent inputs for adding sub-steps. */
    public array $subInputs = [];

    /** @var array<string, class-string<Model>> */
    private const MODELS = [
        'scholarship' => Scholarship::class,
        'job' => JobApplication::class,
        'volunteer' => VolunteerActivity::class,
        'topic' => ScholarshipTopic::class,
        'task' => Task::class,
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

    /** Toggle readiness — syncs the library document when linked, then rolls up. */
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

        // Roll up: sub-checks → parent step → the whole task.
        $this->resyncParent($doc->parent_id);
        $this->resyncTask($doc->documentable_type, $doc->documentable_id);
    }

    /** A parent step becomes done when all its sub-checks are done. */
    private function resyncParent(?int $parentId): void
    {
        if (! $parentId) {
            return;
        }

        $parent = ItemDocument::find($parentId);
        if (! $parent || ! $parent->children()->exists()) {
            return;
        }

        $parent->update(['is_done' => ! $parent->children()->where('is_done', false)->exists()]);
    }

    /** When a task has steps, its progress = % of top-level steps done. */
    private function resyncTask(string $type, int $id): void
    {
        if ($type !== Task::class) {
            return;
        }

        $task = Task::where('user_id', Auth::id())->find($id);
        if (! $task) {
            return;
        }

        $top = ItemDocument::where('documentable_type', Task::class)
            ->where('documentable_id', $id)->whereNull('parent_id')->get();

        if ($top->isEmpty()) {
            return;
        }

        $task->setProgress((int) round($top->where('is_done', true)->count() / $top->count() * 100));
        $task->save();

        $this->dispatch('task-saved');
    }

    /** Add a sub-step under a parent checklist item. */
    public function addSub(int $parentId): void
    {
        $name = trim($this->subInputs[$parentId] ?? '');
        if ($name === '') {
            return;
        }

        $parent = ItemDocument::where('user_id', Auth::id())->find($parentId);
        if (! $parent) {
            return;
        }

        ItemDocument::create([
            'user_id' => Auth::id(),
            'parent_id' => $parent->id,
            'documentable_type' => $parent->documentable_type,
            'documentable_id' => $parent->documentable_id,
            'name' => $name,
            'position' => (int) ItemDocument::where('parent_id', $parent->id)->max('position') + 1,
        ]);

        unset($this->subInputs[$parentId]);

        $this->resyncParent($parent->id);
        $this->resyncTask($parent->documentable_type, $parent->documentable_id);
    }

    public function saveNote(int $id, ?string $note): void
    {
        ItemDocument::where('user_id', Auth::id())->where('id', $id)
            ->update(['note' => $note !== '' ? $note : null]);
    }

    public function delete(int $id): void
    {
        $doc = ItemDocument::where('user_id', Auth::id())->find($id);
        if (! $doc) {
            return;
        }

        $parentId = $doc->parent_id;
        $type = $doc->documentable_type;
        $documentableId = $doc->documentable_id;

        $doc->delete();

        $this->resyncParent($parentId);
        $this->resyncTask($type, $documentableId);
    }

    public function render(): View
    {
        $parent = $this->parent();

        if ($this->allowSubItems) {
            $documents = $parent
                ? $parent->documents()->whereNull('parent_id')
                    ->with(['children.generalDocument', 'generalDocument'])->get()
                : collect();
            $totalCount = $documents->sum(fn (ItemDocument $d) => 1 + $d->children->count());
            $doneCount = $documents->sum(fn (ItemDocument $d) => ($d->isReady() ? 1 : 0) + $d->children->filter(fn (ItemDocument $c) => $c->isReady())->count());
        } else {
            $documents = $parent ? $parent->documents()->with('generalDocument')->get() : collect();
            $totalCount = $documents->count();
            $doneCount = $documents->filter(fn (ItemDocument $d) => $d->isReady())->count();
        }

        return view('livewire.career.item-documents', [
            'documents' => $documents,
            'doneCount' => $doneCount,
            'totalCount' => $totalCount,
            'library' => $this->showLibrary ? ScholarshipDocument::ownedBy(Auth::user())->orderBy('name')->get() : collect(),
        ]);
    }
}
