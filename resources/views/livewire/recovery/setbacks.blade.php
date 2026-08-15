<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('recovery.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← التعافي</a>
        <div class="mt-1 mb-5">
            <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">💔 الانتكاسات</h1>
            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">كل يوم انتكست فيه — الوقت والأسباب وحالة نومك — عشان تشوف النمط وتكسره.</p>
        </div>

        @if ($setbacks->isEmpty())
            <div class="text-center py-16 rounded-2xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                <p class="text-4xl mb-3">🌱</p>
                <p class="text-ink-soft dark:text-ink-dark-soft">مفيش انتكاسات مسجّلة — استمر 🤍</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($setbacks as $log)
                    @php($night = $nights->get($log->date->toDateString()))
                    <div wire:key="sb-{{ $log->id }}" class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5 border-s-4 border-danger">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-ink dark:text-ink-dark">{{ $log->date->translatedFormat('l، j M Y') }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-danger/15 text-danger">انتكاسة</span>
                        </div>

                        {{-- Time + metrics --}}
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-ink-soft dark:text-ink-dark-soft">
                            @if ($log->hardest_from && $log->hardest_to)
                                <span>⏰ أصعب فترة: {{ \Illuminate\Support\Carbon::parse($log->hardest_from)->translatedFormat('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($log->hardest_to)->translatedFormat('g:i A') }}</span>
                            @endif
                            @if ($log->urge_level)<span>🌊 الرغبة: {{ $log->urge_level }}/10</span>@endif
                            @if ($log->mood)<span>😊 المزاج: {{ $log->mood }}/10</span>@endif
                        </div>

                        {{-- Night status for that day --}}
                        <div class="mt-2">
                            @if ($night && $night->status === 'missed')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-danger/15 text-danger">😴 سهرت اليوم ده</span>
                            @elseif ($night && $night->status === 'done')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-success/15 text-success">🌙 غذّيت نفسي واستعديت للنوم</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-ink-soft/10 text-ink-soft dark:text-ink-dark-soft">🌙 النوم/التغذية: مش مسجّل</span>
                            @endif
                        </div>

                        {{-- Causes --}}
                        @if ($log->trigger_note)
                            <p class="text-sm text-ink dark:text-ink-dark mt-3"><span class="text-danger font-medium">المُحفّز:</span> {{ $log->trigger_note }}</p>
                        @endif
                        @if ($log->note)
                            <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1 whitespace-pre-line">{{ $log->note }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
