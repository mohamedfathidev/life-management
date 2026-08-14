<?php

namespace App\Livewire\Scholarships;

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
        $doc->update(['is_ready' => ! $doc->is_ready]);
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
        $documents = ScholarshipDocument::ownedBy(Auth::user())->orderBy('position')->orderBy('id')->get();

        return view('livewire.scholarships.documents', [
            'documents' => $documents,
            'readyCount' => $documents->where('is_ready', true)->count(),
        ]);
    }
}
