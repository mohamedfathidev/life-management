{{-- Verse of the day, framed with the ornamental Quranic brackets ﴿ ﴾ --}}
<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-2xl p-6 sm:p-8 text-center shadow-sm bg-gradient-to-br from-primary/10 via-secondary/10 to-transparent dark:from-primary-dark/15 dark:via-secondary-dark/10 dark:to-transparent']) }}>
    {{-- decorative inner frame --}}
    <div class="pointer-events-none absolute inset-2 rounded-xl border border-primary/25 dark:border-primary-dark/25"></div>
    <div class="pointer-events-none absolute inset-3.5 rounded-lg border border-primary/10 dark:border-primary-dark/10"></div>

    <div class="relative">
        <p class="text-[11px] tracking-widest text-primary dark:text-primary-dark mb-4">آية اليوم</p>

        <p class="text-xl sm:text-2xl leading-[2.4] font-semibold text-ink dark:text-ink-dark px-2" style="font-family: 'Cairo', serif;">
            <span class="text-primary/70 dark:text-primary-dark/70 text-2xl align-middle">﴿</span>
            {{ $verse['text'] }}
            <span class="text-primary/70 dark:text-primary-dark/70 text-2xl align-middle">﴾</span>
        </p>

        <div class="flex items-center justify-center gap-3 mt-5">
            <span class="h-px w-8 bg-primary/30 dark:bg-primary-dark/30"></span>
            <span class="text-sm text-ink-soft dark:text-ink-dark-soft">
                سورة {{ $verse['surah'] }}
                @isset($verse['ayah_number']) : {{ $verse['ayah_number'] }} @endisset
            </span>
            <span class="h-px w-8 bg-primary/30 dark:bg-primary-dark/30"></span>
        </div>

        @isset($verse['theme'])
            <p class="text-xs text-primary/80 dark:text-primary-dark/80 mt-3">{{ $verse['theme'] }}</p>
        @endisset
    </div>
</div>
