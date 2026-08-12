@php($stateColors = ['jamaah' => 'primary', 'ontime' => 'success', 'prayed' => 'warning', 'none' => 'ink-soft'])
<div class="py-10 px-4">
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('arena.index') }}" wire:navigate class="text-sm text-primary dark:text-primary-dark hover:underline">← الساحة</a>

        {{-- Header --}}
        <div class="mt-3 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-2xl font-bold text-ink dark:text-ink-dark">{{ $challenge->name }}</h1>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-ink-soft dark:text-ink-dark-soft">
                        <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary dark:text-primary-dark">{{ $challenge->statusLabel() }}</span>
                        <span>📅 {{ $challenge->start_date->translatedFormat('j M Y') }}@if ($challenge->end_date) → {{ $challenge->end_date->translatedFormat('j M Y') }}@endif</span>
                    </div>
                    @if ($challenge->description)<p class="text-sm text-ink-soft dark:text-ink-dark-soft mt-2">{{ $challenge->description }}</p>@endif
                </div>
                @if ($isOwner)
                    <a href="{{ route('arena.challenges.edit', $challenge) }}" wire:navigate class="shrink-0 text-xs text-primary dark:text-primary-dark hover:underline">تعديل</a>
                @endif
            </div>
        </div>

        {{-- Leaderboard --}}
        <div class="mt-6">
            <h2 class="text-lg font-bold text-ink dark:text-ink-dark mb-3">🏆 لوحة الشرف</h2>
            @php($top = $leaderboard->take(3))
            <div class="grid grid-cols-3 gap-2 mb-3">
                @foreach ($top as $i => $row)
                    <div class="rounded-2xl p-4 text-center {{ $row['isMe'] ? 'bg-primary/10 ring-1 ring-primary/30' : 'bg-surface-light dark:bg-surface-dark' }} shadow-sm {{ $i === 0 ? 'order-2 -mt-2' : ($i === 1 ? 'order-1' : 'order-3') }}">
                        <div class="text-2xl">{{ ['🥇', '🥈', '🥉'][$i] }}</div>
                        <p class="text-sm font-semibold text-ink dark:text-ink-dark mt-1 truncate">{{ $row['name'] }}@if ($row['isOwner']) 👑@endif</p>
                        <p class="text-lg font-bold text-primary dark:text-primary-dark">{{ $row['total'] }}</p>
                    </div>
                @endforeach
            </div>
            @if ($leaderboard->count() > 3)
                <div class="rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm divide-y divide-ink-soft/10 dark:divide-ink-dark-soft/10">
                    @foreach ($leaderboard->slice(3) as $i => $row)
                        <div class="flex items-center justify-between gap-3 px-4 py-3 {{ $row['isMe'] ? 'bg-primary/5' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-ink-soft dark:text-ink-dark-soft w-5 text-center">{{ $i + 4 }}</span>
                                <span class="text-sm text-ink dark:text-ink-dark">{{ $row['name'] }}@if ($row['isOwner']) 👑@endif @if ($row['isMe'])<span class="text-xs text-primary dark:text-primary-dark">(إنت)</span>@endif</span>
                            </div>
                            <span class="text-sm font-bold text-primary dark:text-primary-dark">{{ $row['total'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Daily entry --}}
        <div class="mt-6 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-ink dark:text-ink-dark">سجّل يومك</h2>
                <div class="flex items-center gap-2 text-sm">
                    <button type="button" wire:click="changeDate(-1)" @disabled($isMinDate) class="p-1 rounded disabled:opacity-30 text-ink-soft dark:text-ink-dark-soft hover:text-primary">‹</button>
                    <span class="text-ink dark:text-ink-dark">{{ $dateLabel }}</span>
                    <button type="button" wire:click="changeDate(1)" @disabled($isMaxDate) class="p-1 rounded disabled:opacity-30 text-ink-soft dark:text-ink-dark-soft hover:text-primary">›</button>
                </div>
            </div>

            {{-- Prayers --}}
            @if ($challenge->scoring['prayer']['enabled'] ?? false)
                <div class="space-y-2 mb-5">
                    @foreach ($prayerLabels as $key => $label)
                        <div class="flex items-center gap-2">
                            <span class="w-14 text-sm text-ink dark:text-ink-dark shrink-0">{{ $label }}</span>
                            <div class="flex-1 grid grid-cols-4 gap-1">
                                @foreach ($stateLabels as $st => $stLabel)
                                    @php($active = ($prayers[$key] ?? 'none') === $st)
                                    <button type="button" wire:click="setPrayer('{{ $key }}', '{{ $st }}')"
                                        class="py-1.5 rounded-lg text-xs font-medium transition {{ $active ? 'bg-'.$stateColors[$st].' text-white' : 'bg-bg-light dark:bg-bg-dark text-ink-soft dark:text-ink-dark-soft hover:bg-ink-soft/10' }}">
                                        {{ $stLabel }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Wird --}}
            @if ($challenge->scoring['wird']['enabled'] ?? false)
                <div class="flex items-center gap-3 mb-5">
                    <span class="text-sm text-ink dark:text-ink-dark">📖 صفحات الورد</span>
                    <input type="number" min="0" wire:model.live="wirdPages" class="w-24 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-ink-dark focus:border-primary focus:ring-primary text-sm text-center" />
                </div>
            @endif

            {{-- Extras --}}
            @if (! empty($challenge->scoring['extras']))
                <div class="space-y-2 mb-5">
                    @foreach ($challenge->scoring['extras'] as $extra)
                        <label class="flex items-center gap-2 text-sm text-ink dark:text-ink-dark">
                            <input type="checkbox" wire:model.live="extrasDone.{{ $extra['key'] }}" class="rounded border-gray-300 dark:border-gray-600 text-primary focus:ring-primary" />
                            ✨ {{ $extra['label'] }} <span class="text-xs text-ink-soft dark:text-ink-dark-soft">(+{{ $extra['points'] }})</span>
                        </label>
                    @endforeach
                </div>
            @endif

            <div class="flex items-center justify-between pt-3 border-t border-ink-soft/10 dark:border-ink-dark-soft/10">
                <span class="text-sm text-ink-soft dark:text-ink-dark-soft">نقاط اليوم: <span class="font-bold text-primary dark:text-primary-dark text-base">{{ $livePoints }}</span></span>
                <div class="flex items-center gap-3">
                    <span x-data="{ show: false }" x-on:entry-saved.window="show = true; setTimeout(() => show = false, 1500)" x-show="show" x-cloak class="text-xs text-success">اتسجّل ✓</span>
                    <button type="button" wire:click="saveEntry" class="px-5 py-2 rounded-lg bg-primary dark:bg-primary-dark text-white text-sm font-medium hover:opacity-90 transition">حفظ اليوم</button>
                </div>
            </div>
        </div>

        {{-- Invite (owner) --}}
        @if ($isOwner)
            <div class="mt-6 rounded-2xl bg-surface-light dark:bg-surface-dark shadow-sm p-5">
                <p class="text-sm font-medium text-ink dark:text-ink-dark mb-2">ادعُ أصحابك 🔗 — الكود: <span class="font-mono font-bold text-primary dark:text-primary-dark tracking-widest">{{ $challenge->join_code }}</span></p>
                <div x-data="{ copied: false }" class="flex items-center gap-2">
                    <input type="text" readonly value="{{ $inviteUrl }}" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-xs text-ink-soft dark:text-ink-dark-soft" dir="ltr" />
                    <button type="button" @click="navigator.clipboard.writeText('{{ $inviteUrl }}'); copied = true; setTimeout(() => copied = false, 1500)"
                        class="shrink-0 px-3 py-2 rounded-lg bg-primary/10 text-primary dark:text-primary-dark text-xs hover:bg-primary/20">
                        <span x-text="copied ? 'اتنسخ ✓' : 'انسخ الرابط'"></span>
                    </button>
                </div>
            </div>
        @endif

        @unless ($isOwner)
            <div class="mt-6 text-center">
                <button type="button" wire:click="leave" wire:confirm="متأكد تسيب التحدي؟" class="text-xs text-danger hover:underline">مغادرة التحدي</button>
            </div>
        @endunless
    </div>
</div>
