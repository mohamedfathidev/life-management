<?php

namespace App\Livewire\Scholarships;

use App\Models\ItemDocument;
use App\Models\ScholarshipDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * A checklist of scholarship documents the user can tick off + upload files to.
 */
#[Layout('layouts.app')]
class Documents extends Component
{
    use WithFileUploads;

    public string $newName = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> keyed by document id */
    public array $uploads = [];

    /** Per-document inputs for adding sub-checks. */
    public array $subInputs = [];

    /** Commonly-required scholarship documents. */
    private const COMMON = [
        'السيرة الذاتية (CV)',
        'خطاب الدوافع (SOP)',
        'خطابات التوصية',
        'كشف الدرجات (Transcript)',
        'الشهادة الجامعية',
        'شهادة اللغة (IELTS/TOEFL)',
        'جواز السفر',
        'صورة شخصية',
        'الخطة البحثية (Research Proposal)',
    ];

    public function addDocument(): void
    {
        $this->validate(['newName' => ['required', 'string', 'max:255']], attributes: ['newName' => 'اسم الورقة']);

        ScholarshipDocument::create([
            'user_id' => Auth::id(),
            'name' => $this->newName,
            'position' => (int) ScholarshipDocument::ownedBy(Auth::user())->max('position') + 1,
        ]);

        $this->reset('newName');
    }

    /** Seed the common documents (skipping any the user already has). */
    public function addCommon(): void
    {
        $existing = ScholarshipDocument::ownedBy(Auth::user())->pluck('name')->all();
        $position = (int) ScholarshipDocument::ownedBy(Auth::user())->max('position');

        foreach (self::COMMON as $name) {
            if (! in_array($name, $existing, true)) {
                ScholarshipDocument::create(['user_id' => Auth::id(), 'name' => $name, 'position' => ++$position]);
            }
        }
    }

    public function toggleReady(int $id): void
    {
        $doc = ScholarshipDocument::ownedBy(Auth::user())->findOrFail($id);

        // When it has sub-checks, readiness is derived — not manually toggled.
        if ($doc->documents()->exists()) {
            return;
        }

        $doc->update(['is_ready' => ! $doc->is_ready]);
    }

    public function addSubCheck(int $docId): void
    {
        $name = trim($this->subInputs[$docId] ?? '');
        if ($name === '') {
            return;
        }

        $doc = ScholarshipDocument::ownedBy(Auth::user())->find($docId);
        if (! $doc) {
            return;
        }

        $doc->documents()->create([
            'user_id' => Auth::id(),
            'name' => $name,
            'position' => (int) $doc->documents()->max('position') + 1,
        ]);

        unset($this->subInputs[$docId]);
        $this->syncReady($doc);
    }

    public function toggleSubCheck(int $subId): void
    {
        $sub = ItemDocument::where('user_id', Auth::id())->find($subId);
        if (! $sub) {
            return;
        }
        $sub->update(['is_done' => ! $sub->is_done]);

        if ($sub->documentable instanceof ScholarshipDocument) {
            $this->syncReady($sub->documentable);
        }
    }

    public function deleteSubCheck(int $subId): void
    {
        $sub = ItemDocument::where('user_id', Auth::id())->find($subId);
        if (! $sub) {
            return;
        }
        $parent = $sub->documentable;
        $sub->delete();

        if ($parent instanceof ScholarshipDocument) {
            $this->syncReady($parent);
        }
    }

    /** A document with sub-checks is ready only when all of them are checked. */
    private function syncReady(ScholarshipDocument $doc): void
    {
        if ($doc->documents()->exists()) {
            $doc->update(['is_ready' => ! $doc->documents()->where('is_done', false)->exists()]);
        }
    }

    /** A file was chosen for a document row → store it. */
    public function updatedUploads($value, $key): void
    {
        $this->validate([
            'uploads.'.$key => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ], attributes: ['uploads.'.$key => 'الملف']);

        $doc = ScholarshipDocument::ownedBy(Auth::user())->find($key);
        if (! $doc || ! $value) {
            return;
        }

        if ($doc->file_path) {
            Storage::disk('local')->delete($doc->file_path);
        }

        $path = $value->store('scholarship-docs/'.Auth::id(), 'local');

        $doc->update([
            'file_path' => $path,
            'original_name' => $value->getClientOriginalName(),
            'is_ready' => true,
        ]);

        unset($this->uploads[$key]);
    }

    public function removeFile(int $id): void
    {
        $doc = ScholarshipDocument::ownedBy(Auth::user())->findOrFail($id);
        if ($doc->file_path) {
            Storage::disk('local')->delete($doc->file_path);
        }
        $doc->update(['file_path' => null, 'original_name' => null]);
    }

    public function delete(int $id): void
    {
        $doc = ScholarshipDocument::ownedBy(Auth::user())->findOrFail($id);
        if ($doc->file_path) {
            Storage::disk('local')->delete($doc->file_path);
        }
        $doc->delete();
    }

    public function render(): View
    {
        $documents = ScholarshipDocument::ownedBy(Auth::user())->with('documents')->orderBy('position')->orderBy('id')->get();

        return view('livewire.scholarships.documents', [
            'documents' => $documents,
            'readyCount' => $documents->where('is_ready', true)->count(),
        ]);
    }
}
