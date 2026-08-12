<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        @if ($selected)
            {{-- ============ FOCUS SCREEN (distraction-free) ============ --}}
            <div class="text-center"
                 wire:key="focus-{{ $selected['type'] }}-{{ $selected['id'] }}"
                 x-data="{
                    elapsed: 0,
                    running: false,
                    _int: null,
                    base: {{ $selected['seconds'] }},
                    start() { if (this.running) return; this.running = true; this._int = setInterval(() => this.elapsed++, 1000); },
                    stop() {
                        if (this._int) clearInterval(this._int);
                        this.running = false;
                        if (this.elapsed > 0) { $wire.saveFocus(this.elapsed); this.base += this.elapsed; this.elapsed = 0; }
                    },
                    fmt(s) {
                        const h = Math.floor(s / 3600), m = Math.floor((s % 3600) / 60), sec = s % 60;
                        return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
                    }
                 }"
                 x-init="() => { window.onbeforeunload = () => { if (elapsed > 0) { $wire.saveFocus(elapsed); } }; }">

                <button type="button" wire:click="clearSelection" class="text-sm text-primary dark:text-primary-dark hover:underline mb-8 inline-block">← اختَر حاجة تانية</button>

                <div class="text-6xl mb-4">{{ $selected['emoji'] }}</div>
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark mb-2">{{ $selected['title'] }}</h1>
                <p class="text-xs text-ink-soft dark:text-ink-dark-soft mb-10">ركّز على دي بس. سيبك من الباقي دلوقتي 🤍</p>

                {{-- Stopwatch --}}
                <div class="text-6xl sm:text-7xl font-mono font-bold text-ink dark:text-ink-dark tabular-nums mb-2" dir="ltr" x-text="fmt(elapsed)"></div>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mb-8">
                    إجمالي النهاردة على دي: <span class="font-semibold text-primary dark:text-primary-dark" x-text="fmt(base + elapsed)"></span>
                </p>

                {{-- Controls --}}
                <div class="flex items-center justify-center gap-3">
                    <button type="button" x-show="! running" @click="start()"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-primary dark:bg-primary-dark text-white text-lg font-medium shadow-lg hover:opacity-90 transition">
                        ▶️ ابدأ
                    </button>
                    <button type="button" x-show="running" x-cloak @click="stop()"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-full bg-danger text-white text-lg font-medium shadow-lg hover:opacity-90 transition">
                        ⏹️ إيقاف
                    </button>
                </div>

                <div class="mt-10 flex items-center justify-center gap-4">
                    @if ($selected['done'])
                        <span class="text-sm text-success">✓ متعلّمة إنها تمّت النهاردة</span>
                    @else
                        <button type="button" @click="if (running) stop(); $wire.markDone()" class="text-sm text-success hover:underline">علّمها تمّت ✓</button>
                    @endif
                </div>
            </div>
        @else
            {{-- ============ SELECTION SCREEN ============ --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">التركيز 🎯</h1>
                <p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-1">اختَر حاجة واحدة من اللى عليك النهاردة، وشغّل المؤقّت وركّز عليها بس.</p>
            </div>

            @if (! count($groups))
                <div class="text-center py-16 rounded-xl border border-dashed border-ink-soft/30 dark:border-ink-dark-soft/30">
                    <p class="text-4xl mb-3">🌤️</p>
                    <p class="text-ink-soft dark:text-ink-dark-soft">مفيش حاجات مطلوبة النهاردة دلوقتي. ضيف تاسك أو علّم عاداتك.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($groups as $group)
                        <div>
                            <p class="text-xs font-medium text-ink-soft dark:text-ink-dark-soft mb-2">{{ $group['title'] }}</p>
                            <div class="space-y-2">
                                @foreach ($group['items'] as $item)
                                    <button type="button" wire:click="select('{{ $item['type'] }}', {{ $item['id'] }})" wire:key="pick-{{ $item['type'] }}-{{ $item['id'] }}"
                                        class="w-full flex items-center gap-3 text-start rounded-xl bg-surface-light dark:bg-surface-dark shadow-sm hover:shadow-md hover:ring-1 hover:ring-primary/30 transition p-4 {{ $item['done'] ? 'opacity-60' : '' }}">
                                        <span class="text-xl shrink-0">{{ $item['emoji'] }}</span>
                                        <span class="flex-1 min-w-0">
                                            <span class="block text-sm font-medium text-ink dark:text-ink-dark truncate {{ $item['done'] ? 'line-through' : '' }}">{{ $item['title'] }}</span>
                                            @if ($item['focusLabel'])<span class="block text-xs text-ink-soft dark:text-ink-dark-soft mt-0.5">⏱️ ركّزت عليها {{ $item['focusLabel'] }} النهاردة</span>@endif
                                        </span>
                                        <span class="text-primary dark:text-primary-dark text-sm shrink-0">ركّز ←</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</div>
