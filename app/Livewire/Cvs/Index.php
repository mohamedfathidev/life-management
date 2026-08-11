<?php

namespace App\Livewire\Cvs;

use App\Models\Cv;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithFileUploads;

    public bool $uploadOpen = false;

    public string $title = '';
    public ?string $target = null;
    public $file = null;

    public function openUpload(): void
    {
        $this->reset(['title', 'target', 'file']);
        $this->resetValidation();
        $this->uploadOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'target' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'], // 10MB PDF
        ], attributes: [
            'title' => 'العنوان',
            'target' => 'موجّه لـ',
            'file' => 'الملف',
        ]);

        $path = $this->file->store('cvs/'.Auth::id(), 'local');

        Cv::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'target' => $this->target,
            'file_path' => $path,
            'original_name' => $this->file->getClientOriginalName(),
            'size' => $this->file->getSize(),
        ]);

        $this->reset(['title', 'target', 'file']);
        $this->uploadOpen = false;
    }

    public function delete(int $cvId): void
    {
        $cv = Cv::findOrFail($cvId);
        $this->authorize('delete', $cv);

        Storage::disk('local')->delete($cv->file_path);
        $cv->delete();
    }

    public function render(): View
    {
        $cvs = Cv::query()
            ->ownedBy(Auth::user())
            ->latest()
            ->get();

        return view('livewire.cvs.index', [
            'cvs' => $cvs,
        ]);
    }
}
