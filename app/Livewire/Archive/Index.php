<?php

namespace App\Livewire\Archive;

use App\Services\ArchiveService;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The app-wide archive: a searchable, paginated history of every record
 * across modules (excluding the PIN-protected Diary & Recovery).
 */
#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $type = '';

    #[Url]
    public ?string $from = null;

    #[Url]
    public ?string $to = null;

    private const PER_PAGE = 20;

    public function updated($name): void
    {
        // Any filter change resets to the first page.
        if (in_array($name, ['search', 'type', 'from', 'to'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'type', 'from', 'to');
        $this->resetPage();
    }

    public function render(): View
    {
        $all = ArchiveService::for(Auth::user())->records($this->type, $this->search, $this->from, $this->to);

        $page = $this->getPage();
        $items = $all->forPage($page, self::PER_PAGE)->values();

        $records = new LengthAwarePaginator(
            $items,
            $all->count(),
            self::PER_PAGE,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page'],
        );

        return view('livewire.archive.index', [
            'records' => $records,
            'total' => $all->count(),
            'types' => ArchiveService::types(),
        ]);
    }
}
