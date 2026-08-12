<?php

namespace App\View\Components;

use Illuminate\Support\Carbon;
use Illuminate\View\Component;

class QuranQuote extends Component
{
    /** @var array<string, mixed> */
    public array $verse = [];

    public function __construct()
    {
        $this->verse = $this->pickVerseOfTheDay();
    }

    /** @return array<string, mixed> */
    private function pickVerseOfTheDay(): array
    {
        $path = public_path('files/Quran-Quotes.json');

        if (! is_file($path)) {
            return [];
        }

        $data = json_decode((string) file_get_contents($path), true);
        $verses = $data['verses'] ?? [];

        if (! is_array($verses) || $verses === []) {
            return [];
        }

        // Deterministic per day so it stays the same all day but rotates daily.
        $index = Carbon::today()->dayOfYear % count($verses);

        return $verses[$index] ?? $verses[0];
    }

    public function shouldRender(): bool
    {
        return $this->verse !== [];
    }

    public function render()
    {
        return view('components.quran-quote');
    }
}
