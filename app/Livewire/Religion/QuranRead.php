<?php

namespace App\Livewire\Religion;

use App\Models\QuranReadingPosition;
use App\Services\QuranTextService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * "اقرأ" — the actual Quran text, fetched once per surah from a public API
 * and cached forever after that. Shown one real Mushaf page at a time
 * (grouped by the API's own `page` field) instead of one long scroll, and
 * remembers where the user last stopped so "روح للعلامة" can jump straight
 * to it — switching surah and page both if needed.
 */
#[Layout('layouts.app')]
class QuranRead extends Component
{
    #[Url]
    public int $surah = 1;

    /** The actual Mushaf page number (1–604) currently shown, within this surah. */
    #[Url]
    public ?int $mushafPage = null;

    public ?string $error = null;

    public function mount(): void
    {
        // Only fall back to the saved position if nothing was requested via the URL.
        if (! request()->has('surah')) {
            $position = QuranReadingPosition::ownedBy(Auth::user())->first();
            if ($position) {
                $this->surah = $position->surah_number;
                $this->jumpToAyahPage($position->surah_number, $position->ayah_number);
            }
        }
    }

    /**
     * Livewire lifecycle hook — fires whenever `$surah` changes via wire:model.
     * Just browsing to a different surah must NOT touch the saved bookmark —
     * only an explicit markPosition() should. (This used to auto-save the
     * position here, which silently overwrote any real bookmark the moment
     * the user looked at another surah.)
     */
    public function updatedSurah(int $value): void
    {
        if ($value < 1 || $value > 114) {
            $this->surah = 1;

            return;
        }

        $this->mushafPage = null; // reset to the surah's own first page
    }

    public function goToPage(int $page): void
    {
        $this->mushafPage = $page;
    }

    public function markPosition(int $ayahNumber): void
    {
        $this->savePosition($this->surah, $ayahNumber);
        $this->dispatch('position-marked', ayah: $ayahNumber);
    }

    /** "روح للعلامة" — jump to wherever the saved bookmark is, switching surah/page as needed. */
    public function goToBookmark(): void
    {
        $position = QuranReadingPosition::ownedBy(Auth::user())->first();
        if (! $position) {
            return;
        }

        $this->surah = $position->surah_number;
        $this->jumpToAyahPage($position->surah_number, $position->ayah_number);
    }

    private function jumpToAyahPage(int $surahNumber, int $ayahNumber): void
    {
        try {
            $surahData = app(QuranTextService::class)->surah($surahNumber);
        } catch (\Throwable) {
            return;
        }

        foreach ($surahData['ayahs'] as $ayah) {
            if ($ayah['number'] === $ayahNumber) {
                $this->mushafPage = $ayah['page'];

                return;
            }
        }
    }

    private function savePosition(int $surah, int $ayah): void
    {
        QuranReadingPosition::updateOrCreate(
            ['user_id' => Auth::id()],
            ['surah_number' => $surah, 'ayah_number' => $ayah],
        );
    }

    public function render(): View
    {
        $service = app(QuranTextService::class);
        $this->error = null;
        $surahList = [];
        $surahData = null;
        $pageAyahs = [];
        $pages = [];
        $pageIndex = 0;

        try {
            $surahList = $service->surahList();
            $surahData = $service->surah($this->surah);

            $pages = collect($surahData['ayahs'])->pluck('page')->unique()->sort()->values()->all();

            if ($this->mushafPage === null || ! in_array($this->mushafPage, $pages, true)) {
                $this->mushafPage = $pages[0] ?? null;
            }

            $pageIndex = array_search($this->mushafPage, $pages, true) ?: 0;
            $pageAyahs = collect($surahData['ayahs'])->where('page', $this->mushafPage)->values()->all();
        } catch (\Throwable) {
            $this->error = 'تعذّر تحميل القرآن دلوقتي — جرّب تاني بعد شوية.';
        }

        $position = QuranReadingPosition::ownedBy(Auth::user())->first();

        return view('livewire.religion.quran-read', [
            'surahList' => $surahList,
            'surahData' => $surahData,
            'pageAyahs' => $pageAyahs,
            'pages' => $pages,
            'pageIndex' => $pageIndex,
            'position' => $position,
        ]);
    }
}
