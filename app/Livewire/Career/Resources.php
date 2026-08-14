<?php

namespace App\Livewire\Career;

use App\Models\CareerResource;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * A browsable library of links/resources for a career context (scholarships / jobs):
 * known websites, people, channels… to learn about opportunities.
 */
#[Layout('layouts.app')]
class Resources extends Component
{
    use WithFileUploads;

    public string $context = 'scholarship';

    public ?int $editingId = null;
    public string $title = '';
    public ?string $url = null;
    public $image = null; // uploaded image (optional)
    public string $type = 'website';
    public ?string $note = null;

    #[Url]
    public string $typeFilter = '';

    public const TYPES = [
        'website' => '🌐 موقع',
        'person' => '👤 شخص',
        'channel' => '📺 قناة',
        'group' => '👥 مجموعة',
        'guide' => '📄 مقال/دليل',
        'image' => '🖼️ صورة',
        'other' => '📌 أخرى',
    ];

    /** @return array<int, array{title:string,url:string,type:string}> */
    private function suggested(): array
    {
        return $this->context === 'job'
            ? [
                ['title' => 'LinkedIn Jobs', 'url' => 'https://www.linkedin.com/jobs', 'type' => 'website'],
                ['title' => 'Wuzzuf (مصر)', 'url' => 'https://wuzzuf.net', 'type' => 'website'],
                ['title' => 'Indeed', 'url' => 'https://www.indeed.com', 'type' => 'website'],
                ['title' => 'Glassdoor', 'url' => 'https://www.glassdoor.com', 'type' => 'website'],
                ['title' => 'RemoteOK', 'url' => 'https://remoteok.com', 'type' => 'website'],
                ['title' => 'Wellfound (AngelList)', 'url' => 'https://wellfound.com', 'type' => 'website'],
                ['title' => 'We Work Remotely', 'url' => 'https://weworkremotely.com', 'type' => 'website'],
            ]
            : [
                ['title' => 'Opportunity Desk', 'url' => 'https://opportunitydesk.org', 'type' => 'website'],
                ['title' => 'Scholars4Dev', 'url' => 'https://www.scholars4dev.com', 'type' => 'website'],
                ['title' => 'DAAD (منح ألمانيا)', 'url' => 'https://www.daad.de', 'type' => 'website'],
                ['title' => 'Chevening (بريطانيا)', 'url' => 'https://www.chevening.org', 'type' => 'website'],
                ['title' => 'Erasmus+ (الاتحاد الأوروبي)', 'url' => 'https://erasmus-plus.ec.europa.eu', 'type' => 'website'],
                ['title' => 'Fulbright', 'url' => 'https://foreign.fulbrightonline.org', 'type' => 'website'],
                ['title' => 'UN Volunteers (تطوّع)', 'url' => 'https://www.unv.org', 'type' => 'website'],
                ['title' => 'Idealist (فرص تطوّع)', 'url' => 'https://www.idealist.org', 'type' => 'website'],
            ];
    }

    public function mount(string $context = 'scholarship'): void
    {
        $this->context = in_array($context, ['scholarship', 'job'], true) ? $context : 'scholarship';
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:2000'],
            'image' => ['nullable', 'image', 'max:5120'],
            'type' => ['required', 'in:'.implode(',', array_keys(self::TYPES))],
            'note' => ['nullable', 'string', 'max:1000'],
        ], attributes: ['title' => 'العنوان', 'url' => 'الرابط', 'image' => 'الصورة', 'type' => 'النوع']);

        $existing = $this->editingId ? CareerResource::ownedBy(Auth::user())->find($this->editingId) : null;

        // A resource must have at least a link or an image.
        if (! $this->url && ! $this->image && ! $existing?->image_path) {
            $this->addError('url', 'لازم تحط رابط أو ترفع صورة.');

            return;
        }

        $attributes = [
            'title' => $data['title'],
            'url' => $data['url'],
            'type' => $data['type'],
            'note' => $data['note'],
        ];

        if ($this->image) {
            if ($existing?->image_path) {
                Storage::disk('local')->delete($existing->image_path);
            }
            $attributes['image_path'] = $this->image->store('resource-images/'.Auth::id(), 'local');
            if (! $this->url) {
                $attributes['type'] = 'image';
            }
        }

        if ($existing) {
            $existing->update($attributes);
        } else {
            CareerResource::create($attributes + ['user_id' => Auth::id(), 'context' => $this->context]);
        }

        $this->resetForm();
    }

    public function addSuggested(): void
    {
        $existing = CareerResource::ownedBy(Auth::user())->where('context', $this->context)->pluck('url')->all();

        foreach ($this->suggested() as $s) {
            if (! in_array($s['url'], $existing, true)) {
                CareerResource::create($s + ['user_id' => Auth::id(), 'context' => $this->context]);
            }
        }
    }

    public function edit(int $id): void
    {
        $r = CareerResource::ownedBy(Auth::user())->findOrFail($id);
        $this->editingId = $r->id;
        $this->title = $r->title;
        $this->url = $r->url;
        $this->type = $r->type;
        $this->note = $r->note;
    }

    public function delete(int $id): void
    {
        $r = CareerResource::ownedBy(Auth::user())->find($id);
        if ($r?->image_path) {
            Storage::disk('local')->delete($r->image_path);
        }
        $r?->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->reset('editingId', 'title', 'url', 'note', 'image');
        $this->type = 'website';
        $this->resetValidation();
    }

    public function render(): View
    {
        $resources = CareerResource::ownedBy(Auth::user())
            ->where('context', $this->context)
            ->when($this->typeFilter !== '', fn ($q) => $q->where('type', $this->typeFilter))
            ->latest()->get();

        return view('livewire.career.resources', [
            'resources' => $resources,
            'types' => self::TYPES,
            'title_heading' => $this->context === 'job' ? 'مصادر الوظائف' : 'مصادر المنح',
            'backRoute' => $this->context === 'job' ? route('jobs.index') : route('scholarships.index'),
            'backLabel' => $this->context === 'job' ? 'الوظائف' : 'المنح',
        ]);
    }
}
