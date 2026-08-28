<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <a href="{{ route('religion.quran') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← القرآن</a>

        <div class="flex items-center justify-between gap-4 mt-2 mb-6">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">📖 اقرأ</h1>
            @if ($position)
                <button type="button" wire:click="goToBookmark"
                    class="text-xs px-3 py-1.5 rounded-full bg-primary/10 dark:bg-primary-dark/10 text-primary dark:text-primary-dark hover:opacity-80 transition">
                    🔖 روح للعلامة
                </button>
            @endif
        </div>

        @if ($error)
            <div class="text-center py-16 rounded-3xl border-2 border-dashed border-danger/25">
                <p class="text-5xl mb-3">📡</p>
                <p class="text-danger">{{ $error }}</p>
            </div>
        @elseif ($surahData)
            {{-- Surah picker + nav --}}
            <div class="flex items-center gap-2 mb-6">
                <button type="button" wire:click="$set('surah', {{ max(1, $surah - 1) }})" @disabled($surah <= 1)
                    class="shrink-0 w-9 h-9 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm flex items-center justify-center text-ink dark:text-ink-dark disabled:opacity-30">›</button>

                <select wire:model.live="surah" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm">
                    @foreach ($surahList as $s)
                        <option value="{{ $s['number'] }}">{{ $s['number'] }}. {{ $s['name'] }} — {{ $s['numberOfAyahs'] }} آية</option>
                    @endforeach
                </select>

                <button type="button" wire:click="$set('surah', {{ min(114, $surah + 1) }})" @disabled($surah >= 114)
                    class="shrink-0 w-9 h-9 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm flex items-center justify-center text-ink dark:text-ink-dark disabled:opacity-30">‹</button>
            </div>

            {{-- Surah header --}}
            <div class="text-center mb-6 pb-6 border-b border-ink-soft/10 dark:border-ink-dark-soft/10">
                <p class="text-3xl font-bold text-ink dark:text-ink-dark" style="font-family: 'Amiri Quran', serif;">{{ $surahData['name'] }}</p>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mt-1">
                    {{ $surahData['revelationType'] === 'Meccan' ? 'مكية' : 'مدنية' }} · {{ count($surahData['ayahs']) }} آية
                </p>
            </div>

            {{-- Ayahs — one real Mushaf page at a time --}}
            <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6 sm:p-8">
                <p class="text-2xl sm:text-3xl leading-loose text-ink dark:text-ink-dark text-justify" style="font-family: 'Amiri Quran', serif;" dir="rtl">
                    @foreach ($pageAyahs as $ayah)
                        <span wire:key="ayah-{{ $ayah['number'] }}"
                            @class(['cursor-pointer hover:bg-primary/10 dark:hover:bg-primary-dark/10 rounded transition', 'bg-primary/15 dark:bg-primary-dark/15' => $position && $position->surah_number === $surah && $position->ayah_number === $ayah['number']])
                            wire:click="markPosition({{ $ayah['number'] }})" title="اضغط لتحفظ موضعك هنا">
                            {{ trim($ayah['text']) }}
                            <span class="inline-flex items-center justify-center w-6 h-6 mx-1 rounded-full bg-primary/10 dark:bg-primary-dark/15 text-primary dark:text-primary-dark text-xs align-middle" style="font-family: 'Cairo', sans-serif;">{{ $ayah['number'] }}</span>
                        </span>
                    @endforeach
                </p>
            </div>

            {{-- Page nav (within this surah, by real Mushaf page) --}}
            <div class="flex items-center justify-between gap-3 mt-4">
                <button type="button" wire:click="goToPage({{ $pages[$pageIndex - 1] ?? $pages[$pageIndex] }})" @disabled($pageIndex <= 0)
                    class="px-4 py-2 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm text-ink dark:text-ink-dark disabled:opacity-30 transition">→ الصفحة اللي فاتت</button>

                <span class="text-xs text-ink-soft dark:text-ink-dark-soft shrink-0">
                    صفحة {{ $mushafPage }} من المصحف · {{ $pageIndex + 1 }}/{{ count($pages) }} في السورة
                </span>

                <button type="button" wire:click="goToPage({{ $pages[$pageIndex + 1] ?? $pages[$pageIndex] }})" @disabled($pageIndex >= count($pages) - 1)
                    class="px-4 py-2 rounded-lg bg-surface-light dark:bg-surface-dark shadow-sm text-sm text-ink dark:text-ink-dark disabled:opacity-30 transition">الصفحة اللي جاية ←</button>
            </div>

            <p class="text-center text-xs text-ink-soft dark:text-ink-dark-soft mt-4">اضغط على أي آية عشان تحفظ إنك وقفت عندها</p>
        @else
            <div class="text-center py-16">
                <p class="text-ink-soft dark:text-ink-dark-soft">جاري التحميل...</p>
            </div>
        @endif
    </div>
</div>
